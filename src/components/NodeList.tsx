import React from 'react';
import { RankingCard } from './RankingCard';
import { Pagination } from './Pagination';
import { NodeRanking } from '../types';
import { usePagination } from '../hooks/usePagination';

interface NodeListProps {
  nodes: NodeRanking[];
  title: string;
  subtitle?: string;
  showPagination?: boolean;
}

export const NodeList: React.FC<NodeListProps> = ({ 
  nodes, 
  title, 
  subtitle,
  showPagination = true 
}) => {
  const {
    currentPage,
    totalPages,
    paginatedData,
    goToPage,
    totalItems,
    itemsPerPage,
    startIndex
  } = usePagination({
    data: nodes,
    itemsPerPage: 20,
    initialPage: 1
  });

  if (nodes.length === 0) {
    return (
      <div className="text-center py-12">
        <div className="text-gray-400 text-lg">Không có dữ liệu để hiển thị</div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="text-center">
        <h3 className="text-4xl font-bold text-white mb-4">{title}</h3>
        {subtitle && (
          <p className="text-xl text-gray-300">{subtitle}</p>
        )}
        <div className="mt-4 flex items-center justify-center space-x-4 text-sm text-gray-400">
          <span>Trang {currentPage} / {totalPages}</span>
          <span>•</span>
          <span>{totalItems.toLocaleString()} nodes</span>
          <span>•</span>
          <span>{itemsPerPage} nodes/trang</span>
        </div>
      </div>

      {/* Node Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {paginatedData.map((node, index) => (
          <RankingCard 
            key={node.public_key} 
            node={node} 
            index={startIndex + index}
          />
        ))}
      </div>

      {/* Pagination */}
      {showPagination && totalPages > 1 && (
        <div className="mt-12">
          <Pagination
            currentPage={currentPage}
            totalPages={totalPages}
            onPageChange={goToPage}
            totalItems={totalItems}
            itemsPerPage={itemsPerPage}
          />
        </div>
      )}
    </div>
  );
};