@extends('layouts.app')

@section('title', 'Antrean Barista')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/barista.css') }}">
@endpush

@section('content')
<div class="barista-layout">
    <x-sidebar />

    <main class="main-content">
        <x-header title="Barista Display System" />
        
        <div class="barista-container">
            <div class="barista-header">
                <div class="header-title">
                    <h2>Antrean Pesanan</h2>
                    <p>Kitchen Display System (KDS) Real-time</p>
                </div>
                <div id="connection-status" class="status-indicator status-online">
                    <i class="fas fa-circle"></i> Connected
                </div>
            </div>

            <div class="queue-grid" id="queue-container">
                <!-- Data akan dimuat oleh JavaScript -->
                <div class="empty-state">
                    <i class="fas fa-mug-hot"></i>
                    <p>Memuat antrean...</p>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    let queueData = [];
    
    function fetchQueue() {
        fetch('{{ route("barista.index") }}', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            const status = document.getElementById('connection-status');
            status.className = 'status-indicator status-online';
            status.innerHTML = '<i class="fas fa-circle"></i> Connected';
            
            queueData = data;
            renderQueue();
        })
        .catch(err => {
            const status = document.getElementById('connection-status');
            status.className = 'status-indicator status-offline';
            status.innerHTML = '<i class="fas fa-circle"></i> Disconnected';
        });
    }

    function updateStatus(itemId, status) {
        fetch(`/barista/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) fetchQueue();
        });
    }

    function renderQueue() {
        const container = document.getElementById('queue-container');
        if (queueData.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-mug-hot"></i>
                    <p>Belum ada antrean pesanan.</p>
                </div>
            `;
            return;
        }

        let html = '';
        queueData.forEach(order => {
            const isProgress = order.items.some(i => i.status === 'on_progress');
            
            html += `
                <div class="ticket ${isProgress ? 'on_progress' : ''}">
                    <div class="status-ticket-badge ${isProgress ? 'badge-progress' : 'badge-pending'}">
                        ${isProgress ? 'SEDANG DIPROSES' : 'MENUNGGU'}
                    </div>
                    
                    <div class="ticket-header">
                        <span class="ticket-id">#${order.transaction_id.substring(0, 5).toUpperCase()}</span>
                        <span class="ticket-time"><i class="far fa-clock"></i> ${order.order_time}</span>
                    </div>
                    
                    <div class="ticket-customer"><i class="fas fa-user-circle"></i> ${order.customer}</div>
                    
                    <div class="ticket-items">
            `;
            
            order.items.forEach(item => {
                const custom = item.customizations || {};
                let customHtml = '';
                if (custom.size) customHtml += `<div><strong>Size:</strong> ${custom.size}</div>`;
                if (custom.temperature) customHtml += `<div><strong>Temp:</strong> ${custom.temperature}</div>`;
                if (custom.sugar) customHtml += `<div><strong>Sugar:</strong> ${custom.sugar}</div>`;
                if (custom.notes) customHtml += `<div style="color: #d97706;"><strong>Note:</strong> ${custom.notes}</div>`;
                
                html += `
                    <div class="item-row">
                        <div class="item-header">
                            <div class="item-name-qty">
                                <span class="item-qty">${item.quantity}x</span>
                                <div class="item-name">${item.product ? item.product.name : 'Unknown Product'}</div>
                            </div>
                            <span class="item-status-badge ${item.status === 'on_progress' ? 'status-progress' : 'status-pending'}">
                                ${item.status === 'on_progress' ? 'Proses' : 'Pending'}
                            </span>
                        </div>
                        
                        ${customHtml ? `<div class="item-custom">${customHtml}</div>` : ''}
                        
                        <div class="ticket-actions">
                            ${item.status === 'pending' ? 
                                `<button class="btn-action btn-process" onclick="updateStatus('${item.id}', 'on_progress')">
                                    <i class="fas fa-play"></i> Mulai Proses
                                </button>` : 
                                `<button class="btn-action btn-ready" onclick="updateStatus('${item.id}', 'ready')">
                                    <i class="fas fa-check-double"></i> Tandai Selesai
                                </button>`
                            }
                        </div>
                    </div>
                `;
            });
            
            html += `</div></div>`;
        });
        
        container.innerHTML = html;
    }

    // Polling setiap 5 detik
    setInterval(fetchQueue, 5000);
    fetchQueue();
</script>
@endpush
