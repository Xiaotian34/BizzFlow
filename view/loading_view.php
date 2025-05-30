<div class="loading-overlay">
    <div class="loading-spinner"></div>
    <div class="loading-message">Procesando conversión, por favor espera...</div>
</div>
<style>
.loading-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255,255,255,0.85);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.loading-spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 70px;
    height: 70px;
    animation: spin 1.2s linear infinite;
    margin-bottom: 20px;
}
@keyframes spin {
    0% { transform: rotate(0deg);}
    100% { transform: rotate(360deg);}
}
.loading-message {
    font-size: 1.2rem;
    color: #3498db;
    font-weight: bold;
}
</style>