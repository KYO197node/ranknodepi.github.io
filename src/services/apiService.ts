import { mockRankingData } from '../data/mockData';

export interface ApiNodeRanking {
  public_key: string;
  last_active_date: string;
  rank?: number;
  ranking?: number;
}

export interface ApiResponse {
  "xếp hạng": ApiNodeRanking[];
  total_pages: number;
  last_updated_at: string;
}

class ApiService {
  private staticData: ApiResponse | null = null;
  private isLoading = false;

  async loadStaticData(): Promise<ApiResponse> {
    if (this.staticData) {
      return this.staticData;
    }

    if (this.isLoading) {
      while (this.isLoading) {
        await new Promise(resolve => setTimeout(resolve, 100));
      }
      if (this.staticData) return this.staticData;
    }

    this.isLoading = true;

    try {
      // Try to load from static JSON file in assets
      try {
        const response = await fetch('/src/assets/nodes_ranking.json');
        if (response.ok) {
          const data = await response.json();
          
          // Validate data structure
          if (data && data["xếp hạng"] && Array.isArray(data["xếp hạng"])) {
            console.log(`✅ Loaded ${data["xếp hạng"].length} nodes from static file`);
            this.staticData = {
              "xếp hạng": data["xếp hạng"],
              total_pages: data.total_pages || Math.ceil(data["xếp hạng"].length / 20),
              last_updated_at: data.last_updated_at || new Date().toISOString()
            };
            this.isLoading = false;
            return this.staticData;
          }
        }
      } catch (error) {
        console.warn('Failed to load from assets folder:', error);
      }

      // Try to load from data folder
      try {
        const response = await fetch('/src/data/nodes_ranking.json');
        if (response.ok) {
          const data = await response.json();
          
          // Validate data structure
          if (data && data["xếp hạng"] && Array.isArray(data["xếp hạng"])) {
            console.log(`✅ Loaded ${data["xếp hạng"].length} nodes from data folder`);
            this.staticData = {
              "xếp hạng": data["xếp hạng"],
              total_pages: data.total_pages || Math.ceil(data["xếp hạng"].length / 20),
              last_updated_at: data.last_updated_at || new Date().toISOString()
            };
            this.isLoading = false;
            return this.staticData;
          }
        }
      } catch (error) {
        console.warn('Failed to load from data folder:', error);
      }

      // If it's an array (direct node data), wrap it in the expected structure
      try {
        const response = await fetch('/src/assets/nodes_ranking.json');
        if (response.ok) {
          const data = await response.json();
          
          if (Array.isArray(data)) {
            console.log(`✅ Loaded ${data.length} nodes from array format`);
            this.staticData = {
              "xếp hạng": data,
              total_pages: Math.ceil(data.length / 20),
              last_updated_at: new Date().toISOString()
            };
            this.isLoading = false;
            return this.staticData;
          }
        }
      } catch (error) {
        console.warn('Failed to load array format:', error);
      }

      // Fallback to mock data
      console.log('⚠️ Using mock data as fallback');
      this.staticData = mockRankingData;
      this.isLoading = false;
      return mockRankingData;

    } catch (error) {
      console.error('❌ Error loading static data:', error);
      this.isLoading = false;
      
      // Use mock data as final fallback
      this.staticData = mockRankingData;
      return mockRankingData;
    }
  }

  async fetchRankingData(forceRefresh = false): Promise<ApiResponse> {
    // For static data, forceRefresh just reloads the static file
    if (forceRefresh) {
      this.staticData = null;
      console.log('🔄 Force refreshing static data...');
    }
    
    return await this.loadStaticData();
  }

  async searchNode(publicKey: string): Promise<ApiNodeRanking | null> {
    try {
      const data = await this.loadStaticData();
      
      // Search for exact match first
      let node = data["xếp hạng"].find(node => 
        node.public_key === publicKey
      );
      
      // If no exact match, search for partial match
      if (!node) {
        node = data["xếp hạng"].find(node => 
          node.public_key.toLowerCase().includes(publicKey.toLowerCase())
        );
      }
      
      return node || null;
    } catch (error) {
      console.error('Error searching node:', error);
      return null;
    }
  }

  // Get nodes by rank range
  async getNodesByRankRange(startRank: number, endRank: number): Promise<ApiNodeRanking[]> {
    try {
      const data = await this.loadStaticData();
      return data["xếp hạng"].filter(node => {
        const rank = node.rank || node.ranking || 0;
        return rank >= startRank && rank <= endRank;
      });
    } catch (error) {
      console.error('Error getting nodes by rank range:', error);
      return [];
    }
  }

  // Get total node count
  async getTotalNodeCount(): Promise<number> {
    try {
      const data = await this.loadStaticData();
      return data["xếp hạng"].length;
    } catch (error) {
      console.error('Error getting total node count:', error);
      return 0;
    }
  }

  clearCache(): void {
    this.staticData = null;
    console.log('🗑️ Static data cache cleared');
  }

  // Get cache status
  getCacheStatus(): { cached: boolean; age: number; nodeCount: number } {
    return {
      cached: !!this.staticData,
      age: this.staticData ? 0 : 0, // Static data doesn't age
      nodeCount: this.staticData ? this.staticData["xếp hạng"].length : 0
    };
  }

  // Method to update static data (for when user provides new data)
  updateStaticData(newData: ApiNodeRanking[]): void {
    this.staticData = {
      "xếp hạng": newData,
      total_pages: Math.ceil(newData.length / 20),
      last_updated_at: new Date().toISOString()
    };
    console.log(`📊 Updated static data with ${newData.length} nodes`);
  }
}

export const apiService = new ApiService();