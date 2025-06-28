import { useState, useEffect } from 'react';
import { apiService, ApiResponse } from '../services/apiService';
import { mockRankingData } from '../data/mockData';

export const useRankingData = () => {
  const [data, setData] = useState<ApiResponse>(mockRankingData);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [lastUpdated, setLastUpdated] = useState<Date>(new Date());

  const fetchData = async (forceRefresh = false) => {
    setLoading(true);
    setError(null);
    
    try {
      const rankingData = await apiService.fetchRankingData(forceRefresh);
      setData(rankingData);
      setLastUpdated(new Date());
      
      // Clear any previous errors since we successfully loaded data
      setError(null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error occurred');
      console.error('Failed to fetch ranking data:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    // Load static data on component mount
    fetchData();
  }, []);

  return {
    data,
    loading,
    error,
    lastUpdated,
    refetch: () => fetchData(true),
  };
};