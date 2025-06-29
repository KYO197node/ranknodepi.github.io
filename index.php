<?php
require_once 'config.php';

// Đọc dữ liệu từ file JSON
function loadNodeData() {
    $jsonFiles = [
        'data/nodes_ranking.json',
        'assets/nodes_ranking.json',
        'src/data/nodes_ranking.json'
    ];
    
    foreach ($jsonFiles as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            
            if ($data && isset($data['xếp hạng']) && is_array($data['xếp hạng'])) {
                return $data;
            } elseif (is_array($data)) {
                // Nếu là array trực tiếp
                return [
                    'xếp hạng' => $data,
                    'total_pages' => ceil(count($data) / 20),
                    'last_updated_at' => date('c')
                ];
            }
        }
    }
    
    // Fallback data
    return [
        'xếp hạng' => [
            [
                'public_key' => 'GD3TEKP5DUPS4C2NKZD44HNVLTXJML64JSMQF537XEZDVQPVWNFUT7A4',
                'last_active_date' => '2025-06-26T00:00:00.000Z',
                'rank' => 1
            ],
            [
                'public_key' => 'GAR6635PRQPUZZL6QL2HTFIMOGZW5MZ5GIJ5ZDNMQM7PFDHQQQNLSACK',
                'last_active_date' => '2025-06-26T00:00:00.000Z',
                'rank' => 2
            ]
        ],
        'total_pages' => 1,
        'last_updated_at' => date('c')
    ];
}

// Xử lý tìm kiếm
function searchNode($nodes, $query) {
    foreach ($nodes as $node) {
        if ($node['public_key'] === $query || 
            stripos($node['public_key'], $query) !== false) {
            return $node;
        }
    }
    return null;
}

// Lấy dữ liệu
$data = loadNodeData();
$nodes = $data['xếp hạng'];
$totalNodes = count($nodes);

// Xử lý các tham số
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$view = isset($_GET['view']) ? $_GET['view'] : 'top10';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xử lý tìm kiếm
$searchResult = null;
if (!empty($search)) {
    $foundNode = searchNode($nodes, $search);
    $searchResult = [
        'node' => $foundNode ?: ['public_key' => $search, 'last_active_date' => '', 'rank' => 0],
        'found' => $foundNode !== null
    ];
}

// Phân trang
$itemsPerPage = 20;
$totalPages = ceil($totalNodes / $itemsPerPage);
$offset = ($page - 1) * $itemsPerPage;

// Lấy nodes theo view
if ($view === 'all') {
    $displayNodes = array_slice($nodes, $offset, $itemsPerPage);
} else {
    $displayNodes = array_slice($nodes, 0, 10); // Top 10
}

// Helper functions
function formatDate($dateString) {
    return date('d/m/Y', strtotime($dateString));
}

function getRankColor($rank) {
    if ($rank == 1) return 'from-yellow-400 to-yellow-600';
    if ($rank == 2) return 'from-gray-300 to-gray-500';
    if ($rank == 3) return 'from-amber-600 to-amber-800';
    return 'from-purple-400 to-pink-500';
}

function truncateKey($key) {
    return substr($key, 0, 8) . '...' . substr($key, -8);
}

function getRankIcon($rank) {
    if ($rank <= 3) {
        return '<i class="fas fa-trophy"></i>';
    }
    return '<span class="font-bold text-sm">#' . $rank . '</span>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <meta name="keywords" content="Pi Node, Pi Network, Node Ranking, Pi2Team, Blockchain, Cryptocurrency">
    <meta name="author" content="<?php echo TEAM_NAME; ?>">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo SITE_TITLE; ?>">
    <meta property="og:description" content="<?php echo SITE_DESCRIPTION; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo TEAM_NAME; ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Pi Node Ranking - <?php echo TEAM_NAME; ?>">
    <meta name="twitter:description" content="Kiểm tra xếp hạng Pi Node với giao diện đẹp và tính năng đầy đủ.">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #7c3aed 50%, #1e293b 100%);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-pink-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-10 p-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white"><?php echo APP_NAME; ?></h1>
                    <div class="flex items-center space-x-2">
                        <p class="text-sm text-gray-400">Kiểm tra xếp hạng Pi Node</p>
                        <span class="text-xs text-purple-400 font-medium">by <?php echo TEAM_NAME; ?></span>
                        <i class="fas fa-file-alt text-blue-400" title="Static data"></i>
                        <i class="fas fa-database text-green-400" title="<?php echo number_format($totalNodes); ?> nodes loaded"></i>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="location.reload()" class="flex items-center space-x-2 px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-sync-alt"></i>
                    <span class="text-sm font-medium">Làm mới</span>
                </button>
                <div class="text-right">
                    <div class="text-sm text-gray-400">Cập nhật lần cuối</div>
                    <div class="text-sm text-white font-medium">
                        <?php echo date('d/m/Y H:i', strtotime($data['last_updated_at'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="relative z-10 max-w-7xl mx-auto px-6 pb-20">
        <!-- Stats Section -->
        <div class="mb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($totalNodes); ?></div>
                    <div class="text-gray-400 text-sm">Tổng số Node</div>
                    <div class="text-gray-500 text-xs mt-1">Dữ liệu từ file JSON</div>
                </div>

                <div class="group p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2"><?php echo number_format($totalPages); ?></div>
                    <div class="text-gray-400 text-sm">Tổng số trang</div>
                    <div class="text-gray-500 text-xs mt-1">20 nodes/trang</div>
                </div>

                <div class="group p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-code text-white"></i>
                    </div>
                    <div class="text-3xl font-bold text-white mb-2"><?php echo TEAM_NAME; ?></div>
                    <div class="text-gray-400 text-sm">Phát triển bởi</div>
                    <div class="text-gray-500 text-xs mt-1">Hosting PHP</div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="mb-12">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-bold text-white mb-4">
                    Tìm kiếm <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Pi Node</span>
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Nhập Public Key để kiểm tra xếp hạng và thông tin hoạt động của Pi Node
                </p>
                <p class="text-sm text-gray-400 mt-2">
                    Tìm kiếm trong <?php echo number_format($totalNodes); ?> nodes • Phát triển bởi <span class="text-purple-400 font-medium"><?php echo TEAM_NAME; ?></span>
                </p>
            </div>
            
            <form method="GET" class="relative max-w-2xl mx-auto">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Nhập Public Key để tìm kiếm..."
                        class="w-full pl-12 pr-12 py-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                    />
                    <?php if (!empty($search)): ?>
                    <a href="?" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <button
                    type="submit"
                    class="mt-4 w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-purple-500/25 transition-all duration-300 transform hover:scale-[1.02]"
                >
                    Tìm kiếm
                </button>
            </form>
        </div>

        <!-- Search Results -->
        <?php if ($searchResult): ?>
        <div class="mb-12">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-white mb-4 flex items-center justify-center space-x-2">
                    <?php if ($searchResult['found']): ?>
                        <i class="fas fa-check-circle text-green-400"></i>
                        <span>Tìm thấy Pi Node</span>
                    <?php else: ?>
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                        <span>Không tìm thấy Pi Node</span>
                    <?php endif; ?>
                </h3>
            </div>
            
            <?php if ($searchResult['found']): ?>
                <?php 
                $node = $searchResult['node'];
                $rank = $node['rank'] ?? $node['ranking'] ?? 0;
                ?>
                <div class="max-w-2xl mx-auto">
                    <div class="group relative p-6 rounded-2xl backdrop-blur-sm border bg-gradient-to-r from-purple-500/20 to-pink-500/20 border-purple-500/50 shadow-lg shadow-purple-500/10">
                        <div class="absolute -top-3 -left-3 w-12 h-12 bg-gradient-to-r <?php echo getRankColor($rank); ?> rounded-full flex items-center justify-center text-white shadow-lg">
                            <?php echo getRankIcon($rank); ?>
                        </div>

                        <div class="mt-4 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-400 mb-2">Public Key</span>
                                <button onclick="copyToClipboard('<?php echo $node['public_key']; ?>')" class="p-1 rounded-lg hover:bg-white/10 transition-colors duration-200" title="Copy to clipboard">
                                    <i class="fas fa-copy text-gray-400 hover:text-white"></i>
                                </button>
                            </div>
                            <div class="font-mono text-white text-sm bg-black/20 rounded-lg p-3 break-all">
                                <span class="md:hidden"><?php echo truncateKey($node['public_key']); ?></span>
                                <span class="hidden md:inline"><?php echo $node['public_key']; ?></span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-gray-300">
                            <i class="fas fa-calendar"></i>
                            <span class="text-sm">Hoạt động cuối: <?php echo formatDate($node['last_active_date']); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="max-w-2xl mx-auto p-8 rounded-2xl bg-red-500/10 backdrop-blur-sm border border-red-500/20 text-center">
                    <i class="fas fa-exclamation-circle text-red-400 text-5xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-white mb-2">Không tìm thấy</h4>
                    <p class="text-gray-300">
                        Public Key "<span class="font-mono text-red-400"><?php echo htmlspecialchars($search); ?></span>" không có trong danh sách xếp hạng.
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        Đã tìm kiếm trong <?php echo number_format($totalNodes); ?> nodes
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- View Toggle -->
        <?php if (!$searchResult): ?>
        <div class="mb-8">
            <div class="flex justify-center">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-2 flex space-x-2">
                    <a href="?view=top10" class="px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $view === 'top10' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-white/10'; ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Top 10</span>
                    </a>
                    <a href="?view=all&page=1" class="px-6 py-3 rounded-xl font-medium transition-all duration-300 flex items-center space-x-2 <?php echo $view === 'all' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-white/10'; ?>">
                        <i class="fas fa-list"></i>
                        <span>Tất cả (<?php echo number_format($totalNodes); ?>)</span>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content -->
        <?php if (!$searchResult): ?>
        <div>
            <?php if ($view === 'top10'): ?>
                <div class="text-center mb-12">
                    <h3 class="text-4xl font-bold text-white mb-4">
                        Top <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">10</span> Pi Nodes
                    </h3>
                    <p class="text-xl text-gray-300">
                        Danh sách các Pi Node có xếp hạng cao nhất
                    </p>
                    <p class="text-sm text-gray-400 mt-2">
                        Từ tổng số <?php echo number_format($totalNodes); ?> nodes • Phát triển bởi <span class="text-purple-400 font-medium"><?php echo TEAM_NAME; ?></span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($displayNodes as $index => $node): ?>
                        <?php 
                        $rank = $node['rank'] ?? $node['ranking'] ?? ($index + 1);
                        ?>
                        <div class="group relative p-6 rounded-2xl backdrop-blur-sm border bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:scale-[1.02]">
                            <div class="absolute -top-3 -left-3 w-12 h-12 bg-gradient-to-r <?php echo getRankColor($rank); ?> rounded-full flex items-center justify-center text-white shadow-lg">
                                <?php echo getRankIcon($rank); ?>
                            </div>

                            <div class="mt-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400 mb-2">Public Key</span>
                                    <button onclick="copyToClipboard('<?php echo $node['public_key']; ?>')" class="p-1 rounded-lg hover:bg-white/10 transition-colors duration-200" title="Copy to clipboard">
                                        <i class="fas fa-copy text-gray-400 hover:text-white"></i>
                                    </button>
                                </div>
                                <div class="font-mono text-white text-sm bg-black/20 rounded-lg p-3 break-all">
                                    <span class="md:hidden"><?php echo truncateKey($node['public_key']); ?></span>
                                    <span class="hidden md:inline"><?php echo $node['public_key']; ?></span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-calendar"></i>
                                <span class="text-sm">Hoạt động cuối: <?php echo formatDate($node['last_active_date']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- All nodes view with pagination -->
                <div class="text-center mb-12">
                    <h3 class="text-4xl font-bold text-white mb-4">Danh sách tất cả Pi Nodes</h3>
                    <p class="text-xl text-gray-300">Xem toàn bộ danh sách nodes với phân trang 20 nodes/trang</p>
                    <div class="mt-4 flex items-center justify-center space-x-4 text-sm text-gray-400">
                        <span>Trang <?php echo $page; ?> / <?php echo $totalPages; ?></span>
                        <span>•</span>
                        <span><?php echo number_format($totalNodes); ?> nodes</span>
                        <span>•</span>
                        <span>20 nodes/trang</span>
                        <span>•</span>
                        <span class="text-purple-400 font-medium"><?php echo TEAM_NAME; ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    <?php foreach ($displayNodes as $index => $node): ?>
                        <?php 
                        $rank = $node['rank'] ?? $node['ranking'] ?? ($offset + $index + 1);
                        ?>
                        <div class="group relative p-6 rounded-2xl backdrop-blur-sm border bg-white/5 border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:scale-[1.02]">
                            <div class="absolute -top-3 -left-3 w-12 h-12 bg-gradient-to-r <?php echo getRankColor($rank); ?> rounded-full flex items-center justify-center text-white shadow-lg">
                                <?php echo getRankIcon($rank); ?>
                            </div>

                            <div class="mt-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400 mb-2">Public Key</span>
                                    <button onclick="copyToClipboard('<?php echo $node['public_key']; ?>')" class="p-1 rounded-lg hover:bg-white/10 transition-colors duration-200" title="Copy to clipboard">
                                        <i class="fas fa-copy text-gray-400 hover:text-white"></i>
                                    </button>
                                </div>
                                <div class="font-mono text-white text-sm bg-black/20 rounded-lg p-3 break-all">
                                    <span class="md:hidden"><?php echo truncateKey($node['public_key']); ?></span>
                                    <span class="hidden md:inline"><?php echo $node['public_key']; ?></span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-calendar"></i>
                                <span class="text-sm">Hoạt động cuối: <?php echo formatDate($node['last_active_date']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex flex-col items-center space-y-4">
                    <div class="text-sm text-gray-400">
                        Hiển thị <span class="font-medium text-white"><?php echo $offset + 1; ?></span> đến 
                        <span class="font-medium text-white"><?php echo min($offset + $itemsPerPage, $totalNodes); ?></span> trong tổng số 
                        <span class="font-medium text-white"><?php echo number_format($totalNodes); ?></span> nodes
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- First page -->
                        <a href="?view=all&page=1" class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 <?php echo $page == 1 ? 'opacity-50 cursor-not-allowed' : ''; ?> transition-all duration-200" title="Trang đầu">
                            <i class="fas fa-angle-double-left"></i>
                        </a>

                        <!-- Previous page -->
                        <?php if ($page > 1): ?>
                        <a href="?view=all&page=<?php echo $page - 1; ?>" class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200" title="Trang trước">
                            <i class="fas fa-angle-left"></i>
                        </a>
                        <?php else: ?>
                        <span class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 opacity-50 cursor-not-allowed">
                            <i class="fas fa-angle-left"></i>
                        </span>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <div class="flex items-center space-x-1">
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            if ($start > 1) {
                                echo '<a href="?view=all&page=1" class="px-3 py-2 rounded-lg text-sm font-medium bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200">1</a>';
                                if ($start > 2) {
                                    echo '<span class="px-3 py-2 text-gray-400">...</span>';
                                }
                            }
                            
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $page) {
                                    echo '<span class="px-3 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg">' . $i . '</span>';
                                } else {
                                    echo '<a href="?view=all&page=' . $i . '" class="px-3 py-2 rounded-lg text-sm font-medium bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200">' . $i . '</a>';
                                }
                            }
                            
                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) {
                                    echo '<span class="px-3 py-2 text-gray-400">...</span>';
                                }
                                echo '<a href="?view=all&page=' . $totalPages . '" class="px-3 py-2 rounded-lg text-sm font-medium bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200">' . $totalPages . '</a>';
                            }
                            ?>
                        </div>

                        <!-- Next page -->
                        <?php if ($page < $totalPages): ?>
                        <a href="?view=all&page=<?php echo $page + 1; ?>" class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200" title="Trang sau">
                            <i class="fas fa-angle-right"></i>
                        </a>
                        <?php else: ?>
                        <span class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 opacity-50 cursor-not-allowed">
                            <i class="fas fa-angle-right"></i>
                        </span>
                        <?php endif; ?>

                        <!-- Last page -->
                        <a href="?view=all&page=<?php echo $totalPages; ?>" class="p-2 rounded-lg bg-white/5 border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 <?php echo $page == $totalPages ? 'opacity-50 cursor-not-allowed' : ''; ?> transition-all duration-200" title="Trang cuối">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </div>

                    <!-- Quick jump -->
                    <div class="flex items-center space-x-2 text-sm">
                        <span class="text-gray-400">Đi đến trang:</span>
                        <input
                            type="number"
                            min="1"
                            max="<?php echo $totalPages; ?>"
                            class="w-16 px-2 py-1 bg-white/5 border border-white/10 rounded text-white text-center focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="<?php echo $page; ?>"
                            onkeypress="if(event.key==='Enter'){var p=parseInt(this.value);if(p>=1&&p<=<?php echo $totalPages; ?>)location.href='?view=all&page='+p;}"
                        />
                        <span class="text-gray-400">/ <?php echo $totalPages; ?></span>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-white/10 mt-20">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-bold text-white"><?php echo APP_NAME; ?></span>
                        <span class="text-sm text-purple-400 font-medium">by <?php echo TEAM_NAME; ?></span>
                    </div>
                </div>
                <div class="text-gray-400 text-sm text-center md:text-right">
                    <div>© 2025 <?php echo TEAM_NAME; ?>. All rights reserved.</div>
                    <div class="mt-1">
                        Ứng dụng PHP • <?php echo number_format($totalNodes); ?> nodes • 20 nodes/trang
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Đã copy vào clipboard!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Auto-hide loading states
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>