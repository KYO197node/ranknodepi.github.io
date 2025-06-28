export interface NodeRanking {
  public_key: string;
  last_active_date: string;
  rank?: number;
  ranking?: number;
}

export interface RankingData {
  "xếp hạng": NodeRanking[];
  total_pages: number;
  last_updated_at: string;
}

export interface SearchResult {
  node: NodeRanking;
  found: boolean;
}