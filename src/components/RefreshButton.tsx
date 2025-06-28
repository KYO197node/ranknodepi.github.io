import React from 'react';
import { RefreshCw } from 'lucide-react';

interface RefreshButtonProps {
  onRefresh: () => void;
  loading: boolean;
}

export const RefreshButton: React.FC<RefreshButtonProps> = ({ onRefresh, loading }) => {
  return (
    <button
      onClick={onRefresh}
      disabled={loading}
      className={`flex items-center space-x-2 px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white hover:bg-white/20 transition-all duration-300 ${
        loading ? 'cursor-not-allowed opacity-50' : 'hover:scale-105'
      }`}
      title="Làm mới dữ liệu"
    >
      <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
      <span className="text-sm font-medium">
        {loading ? 'Đang tải...' : 'Làm mới'}
      </span>
    </button>
  );
};