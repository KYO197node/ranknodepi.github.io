import React from 'react';
import { AlertTriangle, X } from 'lucide-react';

interface ErrorBannerProps {
  error: string;
  onDismiss: () => void;
}

export const ErrorBanner: React.FC<ErrorBannerProps> = ({ error, onDismiss }) => {
  return (
    <div className="mb-6 p-4 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl flex items-center justify-between">
      <div className="flex items-center space-x-3">
        <AlertTriangle className="w-5 h-5 text-red-400" />
        <div>
          <h4 className="text-red-400 font-medium">Lỗi kết nối API</h4>
          <p className="text-red-300 text-sm">{error}</p>
          <p className="text-red-300 text-xs mt-1">Đang sử dụng dữ liệu cache hoặc mock data</p>
        </div>
      </div>
      <button
        onClick={onDismiss}
        className="text-red-400 hover:text-red-300 transition-colors duration-200"
      >
        <X className="w-5 h-5" />
      </button>
    </div>
  );
};