import React, { useState, useEffect } from 'react';
import { 
  Sparkles, 
  TrendingUp, 
  Users, 
  Clock,
  AlertCircle,
  CheckCircle,
  Database,
  FileText,
  List
} from 'lucide-react';
import { SearchBar } from './components/SearchBar';
import { RankingCard } from './components/RankingCard';
import { StatsCard } from './components/StatsCard';
import { RefreshButton } from './components/RefreshButton';
import { ErrorBanner } from './components/ErrorBanner';
import { NodeList } from './components/NodeList';
import { useRankingData } from './hooks/useRankingData';
import { apiService } from './services/apiService';
import { NodeRanking, SearchResult } from './types';

function App() {
  const [isVisible, setIsVisible] = useState(false);
  const [searchResult, setSearchResult] = useState<SearchResult | null>(null);
  const [isSearching, setIsSearching] = useState(false);
  const [currentView, setCurrentView] = useState<'top10' | 'all'>('top10');
  const [showError, setShowError] = useState(true);
  
  const { data: rankingData, loading, error, lastUpdated, refetch } = useRankingData();

  useEffect(() => {
    setIsVisible(true);
  }, []);

  const handleSearch = async (query: string) => {
    setIsSearching(true);
    
    try {
      const foundNode = await apiService.searchNode(query);
      
      if (foundNode) {
        setSearchResult({ node: foundNode, found: true });
        setCurrentView('top10'); // Reset to top10 view when searching
      } else {
        setSearchResult({ 
          node: { public_key: query, last_active_date: '', rank: 0 }, 
          found: false 
        });
        setCurrentView('top10');
      }
    } catch (err) {
      console.error('Search error:', err);
      setSearchResult({ 
        node: { public_key: query, last_active_date: '', rank: 0 }, 
        found: false 
      });
      setCurrentView('top10');
    }
    
    setIsSearching(false);
  };

  const handleClearSearch = () => {
    setSearchResult(null);
  };

  const formatLastUpdated = (dateString: string) => {
    return new Date(dateString).toLocaleString('vi-VN', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const topNodes = rankingData["xếp hạng"].slice(0, 10);
  const allNodes = rankingData["xếp hạng"];
  const cacheStatus = apiService.getCacheStatus();

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
      {/* Animated background elements */}
      <div className="absolute inset-0">
        <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div className="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-500/5 rounded-full blur-3xl animate-pulse delay-2000"></div>
      </div>

      {/* Navigation */}
      <nav className="relative z-10 p-6">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
              <Sparkles className="w-7 h-7 text-white" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-white">Pi Node Ranking</h1>
              <div className="flex items-center space-x-2">
                <p className="text-sm text-gray-400">Kiểm tra xếp hạng Pi Node</p>
                <FileText className="w-4 h-4 text-blue-400" title="Static data" />
                {cacheStatus.cached && (
                  <Database className="w-4 h-4 text-green-400" title={`${cacheStatus.nodeCount} nodes loaded`} />
                )}
              </div>
            </div>
          </div>
          <div className="flex items-center space-x-4">
            <RefreshButton onRefresh={refetch} loading={loading} />
            <div className="text-right">
              <div className="text-sm text-gray-400">Cập nhật lần cuối</div>
              <div className="text-sm text-white font-medium">
                {formatLastUpdated(rankingData.last_updated_at)}
              </div>
            </div>
          </div>
        </div>
      </nav>

      <div className="relative z-10 max-w-7xl mx-auto px-6 pb-20">
        {/* Error Banner */}
        {error && showError && (
          <ErrorBanner 
            error={error} 
            onDismiss={() => setShowError(false)} 
          />
        )}

        {/* Stats Section */}
        <div className={`mb-12 transform transition-all duration-1000 ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'}`}>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <StatsCard 
              icon={Users} 
              value={rankingData["xếp hạng"].length.toLocaleString()} 
              label="Tổng số Node" 
              subtitle={`Dữ liệu tĩnh từ file JSON`}
            />
            <StatsCard 
              icon={TrendingUp} 
              value={Math.ceil(rankingData["xếp hạng"].length / 20).toLocaleString()} 
              label="Tổng số trang" 
              gradient="from-blue-500 to-cyan-500"
              subtitle="20 nodes/trang"
            />
            <StatsCard 
              icon={FileText} 
              value="Tĩnh" 
              label="Loại dữ liệu" 
              gradient="from-green-500 to-emerald-500"
              subtitle="Từ file JSON local"
            />
          </div>
        </div>

        {/* Search Section */}
        <div className={`mb-12 transform transition-all duration-1000 delay-200 ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'}`}>
          <div className="text-center mb-8">
            <h2 className="text-4xl font-bold text-white mb-4">
              Tìm kiếm <span className="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Pi Node</span>
            </h2>
            <p className="text-xl text-gray-300 max-w-2xl mx-auto">
              Nhập Public Key để kiểm tra xếp hạng và thông tin hoạt động của Pi Node
            </p>
            <p className="text-sm text-gray-400 mt-2">
              Tìm kiếm trong {rankingData["xếp hạng"].length.toLocaleString()} nodes từ dữ liệu tĩnh
            </p>
          </div>
          
          <SearchBar 
            onSearch={handleSearch}
            onClear={handleClearSearch}
            isSearching={isSearching}
          />
        </div>

        {/* Search Results */}
        {searchResult && (
          <div className={`mb-12 transform transition-all duration-500 ${searchResult ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'}`}>
            <div className="text-center mb-8">
              <h3 className="text-2xl font-bold text-white mb-4 flex items-center justify-center space-x-2">
                {searchResult.found ? (
                  <>
                    <CheckCircle className="w-6 h-6 text-green-400" />
                    <span>Tìm thấy Pi Node</span>
                  </>
                ) : (
                  <>
                    <AlertCircle className="w-6 h-6 text-red-400" />
                    <span>Không tìm thấy Pi Node</span>
                  </>
                )}
              </h3>
            </div>
            
            {searchResult.found ? (
              <div className="max-w-2xl mx-auto">
                <RankingCard 
                  node={searchResult.node} 
                  index={0} 
                  isSearchResult={true}
                />
              </div>
            ) : (
              <div className="max-w-2xl mx-auto p-8 rounded-2xl bg-red-500/10 backdrop-blur-sm border border-red-500/20 text-center">
                <AlertCircle className="w-12 h-12 text-red-400 mx-auto mb-4" />
                <h4 className="text-xl font-semibold text-white mb-2">Không tìm thấy</h4>
                <p className="text-gray-300">
                  Public Key "<span className="font-mono text-red-400">{searchResult.node.public_key}</span>" không có trong danh sách xếp hạng.
                </p>
                <p className="text-gray-400 text-sm mt-2">
                  Đã tìm kiếm trong {rankingData["xếp hạng"].length.toLocaleString()} nodes
                </p>
              </div>
            )}
          </div>
        )}

        {/* View Toggle */}
        {!searchResult && (
          <div className={`mb-8 transform transition-all duration-1000 delay-300 ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'}`}>
            <div className="flex justify-center">
              <div className="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-2 flex space-x-2">
                <button
                  onClick={() => setCurrentView('top10')}
                  className={`px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 ${
                    currentView === 'top10'
                      ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
                      : 'text-gray-400 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <TrendingUp className="w-4 h-4" />
                  <span>Top 10</span>
                </button>
                <button
                  onClick={() => setCurrentView('all')}
                  className={`px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 ${
                    currentView === 'all'
                      ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
                      : 'text-gray-400 hover:text-white hover:bg-white/10'
                  }`}
                >
                  <List className="w-4 h-4" />
                  <span>Tất cả ({allNodes.length.toLocaleString()})</span>
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Content based on current view */}
        {!searchResult && (
          <div className={`transform transition-all duration-1000 delay-400 ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'}`}>
            {currentView === 'top10' ? (
              <div>
                <div className="text-center mb-12">
                  <h3 className="text-4xl font-bold text-white mb-4">
                    Top <span className="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">10</span> Pi Nodes
                  </h3>
                  <p className="text-xl text-gray-300">
                    Danh sách các Pi Node có xếp hạng cao nhất
                  </p>
                  <p className="text-sm text-gray-400 mt-2">
                    Từ tổng số {rankingData["xếp hạng"].length.toLocaleString()} nodes trong dữ liệu tĩnh
                  </p>
                  {loading && (
                    <div className="mt-4 flex items-center justify-center space-x-2 text-purple-400">
                      <div className="w-4 h-4 border-2 border-purple-400 border-t-transparent rounded-full animate-spin"></div>
                      <span>Đang tải dữ liệu tĩnh...</span>
                    </div>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {topNodes.map((node, index) => (
                    <RankingCard 
                      key={node.public_key} 
                      node={node} 
                      index={index}
                    />
                  ))}
                </div>
              </div>
            ) : (
              <NodeList
                nodes={allNodes}
                title="Danh sách tất cả Pi Nodes"
                subtitle="Xem toàn bộ danh sách nodes với phân trang 20 nodes/trang"
                showPagination={true}
              />
            )}
          </div>
        )}
      </div>

      {/* Footer */}
      <footer className="relative z-10 border-t border-white/10 mt-20">
        <div className="max-w-7xl mx-auto px-6 py-8">
          <div className="flex flex-col md:flex-row items-center justify-between">
            <div className="flex items-center space-x-3 mb-4 md:mb-0">
              <div className="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                <Sparkles className="w-5 h-5 text-white" />
              </div>
              <span className="text-xl font-bold text-white">Pi Node Ranking</span>
            </div>
            <div className="text-gray-400 text-sm text-center md:text-right">
              <div>© 2025 Pi Node Ranking. All rights reserved.</div>
              <div className="mt-1">
                Dữ liệu tĩnh từ file JSON • {rankingData["xếp hạng"].length.toLocaleString()} nodes • 20 nodes/trang
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

export default App;