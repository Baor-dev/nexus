@extends('layouts.nexus')

@section('content')
<div class="leaderboard-container">
    <div class="leaderboard-header">
        <h1 class="title">🏆 Bảng Xếp Hạng Uy Tín</h1>
        <p class="subtitle">Vinh danh những thành viên có đóng góp tích cực nhất cộng đồng Nexus.</p>
    </div>

    <div class="leaderboard-list">
        @foreach($users as $index => $user)
        <div class="leaderboard-item rank-{{ $index + 1 }}">
            <div class="rank-badge">
                @if($index == 0)
                    <span class="medal gold">🥇</span>
                @elseif($index == 1)
                    <span class="medal silver">🥈</span>
                @elseif($index == 2)
                    <span class="medal bronze">🥉</span>
                @else
                    <span class="rank-number">#{{ $index + 1 }}</span>
                @endif
            </div>

            <!-- CHUYỂN DIV THÀNH A ĐỂ CLICK ĐƯỢC -->
            <a href="{{ route('users.show', $user->id) }}" class="user-info">
                <div class="avatar">
                    @if($user->avatar)
                        <!-- SỬA ĐƯỜNG DẪN ẢNH -->
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <div class="avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                    @endif
                </div>

                <div class="user-details">
                    <div class="user-name">
                        {{ $user->name }}
                        @if($user->role === 'admin')
                            <span class="admin-badge">ADMIN</span>
                        @endif
                    </div>
                    <div class="user-badge">{!! $user->badge !!}</div>
                </div>
            </a>

            <div class="karma-score">
                <span class="karma-value">{{ number_format($user->karma ?? 0) }}</span>
                <span class="karma-label">Karma</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.leaderboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.leaderboard-header {
    text-align: center;
    margin-bottom: 30px;
}

.title {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #1a1a1a;
}

.subtitle {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

.leaderboard-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.leaderboard-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.leaderboard-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.leaderboard-item.rank-1 {
    background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
    border: 2px solid #ffd700;
}

.leaderboard-item.rank-2 {
    background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
    border: 2px solid #c0c0c0;
}

.leaderboard-item.rank-3 {
    background: linear-gradient(135deg, #fff5f0 0%, #ffffff 100%);
    border: 2px solid #cd7f32;
}

.rank-badge {
    flex-shrink: 0;
    width: 50px;
    text-align: center;
}

.medal {
    font-size: 2rem;
}

.rank-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #666;
}

/* CẬP NHẬT STYLE CHO LINK USER-INFO */
.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
    text-decoration: none; /* Bỏ gạch chân */
    color: inherit; /* Giữ màu gốc */
    transition: opacity 0.2s;
}

.user-info:hover {
    opacity: 0.8; /* Hiệu ứng hover nhẹ */
}

.avatar {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    background: #e0e0e0;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    text-transform: uppercase;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.admin-badge {
    display: inline-block;
    padding: 2px 8px;
    background: #ff4444;
    color: white;
    font-size: 0.7rem;
    font-weight: bold;
    border-radius: 4px;
    letter-spacing: 0.5px;
}

.user-badge {
    font-size: 0.9rem;
    color: #666;
    margin-top: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.karma-score {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
    min-width: 80px;
}

.karma-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.karma-label {
    font-size: 0.75rem;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive cho tablet */
@media (max-width: 768px) {
    .leaderboard-container {
        padding: 15px;
    }

    .title {
        font-size: 1.5rem;
    }

    .subtitle {
        font-size: 0.9rem;
    }

    .leaderboard-item {
        padding: 12px;
        gap: 12px;
    }

    .rank-badge {
        width: 40px;
    }

    .medal {
        font-size: 1.5rem;
    }

    .rank-number {
        font-size: 1.2rem;
    }

    .avatar {
        width: 45px;
        height: 45px;
    }

    .user-name {
        font-size: 1rem;
    }

    .karma-value {
        font-size: 1.3rem;
    }
}

/* Responsive cho mobile */
@media (max-width: 480px) {
    .leaderboard-container {
        padding: 10px;
    }

    .title {
        font-size: 1.3rem;
    }

    .subtitle {
        font-size: 0.85rem;
    }

    .leaderboard-list {
        gap: 10px;
    }

    .leaderboard-item {
        padding: 10px;
        gap: 10px;
    }

    .rank-badge {
        width: 35px;
    }

    .medal {
        font-size: 1.3rem;
    }

    .rank-number {
        font-size: 1rem;
    }

    .user-info {
        gap: 10px;
    }

    .avatar {
        width: 40px;
        height: 40px;
    }

    .avatar-placeholder {
        font-size: 1.2rem;
    }

    .user-name {
        font-size: 0.95rem;
    }

    .admin-badge {
        padding: 1px 6px;
        font-size: 0.65rem;
    }

    .user-badge {
        font-size: 0.8rem;
    }

    .karma-score {
        min-width: 70px;
    }

    .karma-value {
        font-size: 1.2rem;
    }

    .karma-label {
        font-size: 0.7rem;
    }
}

/* Responsive cho mobile nhỏ */
@media (max-width: 360px) {
    .leaderboard-item {
        gap: 8px;
    }

    .rank-badge {
        width: 30px;
    }

    .medal {
        font-size: 1.1rem;
    }

    .rank-number {
        font-size: 0.9rem;
    }

    .avatar {
        width: 35px;
        height: 35px;
    }

    .user-name {
        font-size: 0.9rem;
    }

    .karma-score {
        min-width: 60px;
    }

    .karma-value {
        font-size: 1.1rem;
    }
}
</style>
@endsection