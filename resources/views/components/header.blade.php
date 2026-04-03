@props(['title' => 'Dashboard', 'searchPlaceholder' => 'Cari sesuatu...'])

<header class="app-header">
    <div class="header-content">
        <!-- <div class="header-left">
            <img src="{{ asset('main_logo.png') }}" alt="Logo" class="global-logo">
        </div> -->


        <div class="header-center">
            @isset($search)
                <div class="header-search-bar">
                    {{ $search }}
                </div>
            @else
                <div class="header-search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="{{ $searchPlaceholder }}" id="global-header-search">
                </div>
            @endisset
        </div>

        <div class="header-right">
            <div class="system-status">
                <span class="dot pulse"></span>
                <span>Sinkronisasi Berhasil</span>
            </div>
            
            <div class="header-actions">
                <button class="header-icon-btn" title="Lonceng Notifikasi">
                    <i class="far fa-bell"></i>
                    <span class="badge red"></span>
                </button>
                <div class="user-pill" title="Profil Pengguna">
                    <div class="user-text">
                        <span class="nameText">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="roleText">{{ auth()->user()->role ?? 'Admin' }}</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=0052cc&color=fff" alt="User Avatar" class="header-avatar">
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* Premium Header Styles */
    .app-header {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(0, 82, 204, 0.05);
        padding: 0.75rem 0;
        position: sticky;
        top: 0;
        z-index: 99;
        margin-bottom: 1.5rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1.5rem;
        gap: 1.5rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-shrink: 0;
    }

    .global-logo {
        height: 36px;
        width: auto;
        object-fit: contain;
    }

    .page-context {
        display: flex;
        flex-direction: column;
    }

    .page-context .subtitle {
        font-size: 0.65rem;
        font-weight: 700;
        color: #0052cc;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: -2px;
    }

    .page-context .page-title {
        font-size: 1.15rem;
        font-weight: 850;
        color: #172b4d;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .header-center {
        flex: 1;
        max-width: 500px;
    }

    .header-search-bar {
        position: relative;
        width: 100%;
    }

    .header-search-bar i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .header-search-bar input {
        width: 100%;
        padding: 10px 1rem 10px 2.75rem;
        border: 1px solid transparent;
        border-radius: 12px;
        background: rgba(0, 82, 204, 0.05);
        font-size: 0.9rem;
        color: #172b4d;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .header-search-bar input:focus {
        outline: none;
        background: #ffffff;
        border-color: #0052cc;
        box-shadow: 0 4px 12px rgba(0, 82, 204, 0.1);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-shrink: 0;
    }

    .system-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #e6fffa;
        color: #319795;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .system-status .dot {
        width: 6px;
        height: 6px;
        background: #38a169;
        border-radius: 50%;
    }

    .pulse {
        animation: header-pulse 2s infinite;
    }

    @keyframes header-pulse {
        0% { box-shadow: 0 0 0 0 rgba(56, 161, 105, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(56, 161, 105, 0); }
        100% { box-shadow: 0 0 0 0 rgba(56, 161, 105, 0); }
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-icon-btn {
        background: transparent;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5e6c84;
        font-size: 1.1rem;
        cursor: pointer;
        position: relative;
        transition: all 0.2s;
    }

    .header-icon-btn:hover {
        background: rgba(0, 82, 204, 0.08);
        color: #0052cc;
    }

    .badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .badge.red { background: #ef4444; }

    .user-pill {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 4px 4px 4px 12px;
        background: #ffffff;
        border: 1px solid #ebecf0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .user-pill:hover {
        border-color: #0052cc;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .user-text {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .user-text .nameText {
        font-size: 0.8rem;
        font-weight: 700;
        color: #172b4d;
        line-height: 1;
        margin-bottom: 2px;
    }

    .user-text .roleText {
        font-size: 0.65rem;
        color: #5e6c84;
    }

    .header-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        object-fit: cover;
    }

    @media (max-width: 1024px) {
        .system-status { display: none; }
    }

    @media (max-width: 768px) {
        .header-left .page-context, .user-text { display: none; }
        .header-search-bar { max-width: 200px; }
        .app-header { margin-bottom: 1rem; }
    }
</style>
