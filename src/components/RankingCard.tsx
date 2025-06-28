import React from 'react';
import { Trophy, Calendar, Copy, Check } from 'lucide-react';
import { NodeRanking } from '../types';

interface RankingCardProps {
  node: NodeRanking;
  index: number;
  isSearchResult?: boolean;
}

export const RankingCard: React.FC<RankingCardProps> = ({ node, index, isSearchResult = false }) => {
  const [copied, setCopied] = React.useState(false);
  const rank = node.rank || node.ranking || index + 1;

  const getRankColor = (rank: number) => {
    if (rank === 1) return 'from-yellow-400 to-yellow-600';
    if (rank === 2) return 'from-gray-300 to-gray-500';
    if (rank === 3) return 'from-amber-600 to-amber-800';
    return 'from-purple-400 to-pink-500';
  };

  const getRankIcon = (rank: number) => {
    if (rank <= 3) return <Trophy className="w-5 h-5" />;
    return <span className="font-bold text-sm">#{rank}</span>;
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('vi-VN', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(node.public_key);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch (err) {
      console.error('Failed to copy:', err);
    }
  };

  const truncateKey = (key: string) => {
    return `${key.slice(0, 8)}...${key.slice(-8)}`;
  };

  return (
    <div className={`group relative p-6 rounded-2xl backdrop-blur-sm border transition-all duration-300 hover:scale-[1.02] ${
      isSearchResult 
        ? 'bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-purple-500/50 shadow-lg shadow-purple-500/10' 
        : 'bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20'
    }`}>
      {/* Rank Badge */}
      <div className={`absolute -top-3 -left-3 w-12 h-12 bg-gradient-to-r ${getRankColor(rank)} rounded-full flex items-center justify-center text-white shadow-lg`}>
        {getRankIcon(rank)}
      </div>

      {/* Public Key */}
      <div className="mt-4 mb-4">
        <div className="flex items-center justify-between">
          <span className="text-sm text-gray-400 mb-2">Public Key</span>
          <button
            onClick={copyToClipboard}
            className="p-1 rounded-lg hover:bg-white/10 transition-colors duration-200"
            title="Copy to clipboard"
          >
            {copied ? (
              <Check className="w-4 h-4 text-green-400" />
            ) : (
              <Copy className="w-4 h-4 text-gray-400 hover:text-white" />
            )}
          </button>
        </div>
        <div className="font-mono text-white text-sm bg-black/20 rounded-lg p-3 break-all">
          <span className="md:hidden">{truncateKey(node.public_key)}</span>
          <span className="hidden md:inline">{node.public_key}</span>
        </div>
      </div>

      {/* Last Active Date */}
      <div className="flex items-center space-x-2 text-gray-300">
        <Calendar className="w-4 h-4" />
        <span className="text-sm">Hoạt động cuối: {formatDate(node.last_active_date)}</span>
      </div>

      {/* Hover Effect */}
      <div className="absolute inset-0 rounded-2xl bg-gradient-to-r from-purple-500/0 to-pink-500/0 group-hover:from-purple-500/5 group-hover:to-pink-500/5 transition-all duration-300 pointer-events-none"></div>
    </div>
  );
};