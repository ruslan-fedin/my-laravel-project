{{-- resources/views/partials/footer.blade.php --}}
<style>
    .main-footer {
        margin-top: 60px; /* Больше пространства сверху */
        padding: 30px 0;
        border-top: 2px solid #f1f5f9; /* Более мягкая, но четкая линия */
        display: flex;
        justify-content: space-between;
        align-items: flex-start; /* Выравнивание по верхнему краю внутри футера */
    }

    /* ЛЕВАЯ ЧАСТЬ */
    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .footer-copy {
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        color: #1e293b;
        letter-spacing: 0.08em;
    }
    .footer-status {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        background-color: #10b981; /* Зеленый индикатор "Online/Stable" */
        border-radius: 50%;
    }

    /* ПРАВАЯ ЧАСТЬ */
    .footer-author {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .author-label {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.1em;
    }
    .author-name {
        font-size: 13px;
        font-weight: 950;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: -0.01em;
    }
    .author-contact {
        display: inline-flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }
    .contact-link {
        font-size: 10px;
        font-weight: 800;
        color: #2563eb;
        text-decoration: none;
        background: #eff6ff;
        padding: 4px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .contact-link:hover {
        background: #2563eb;
        color: #ffffff;
    }
</style>

<footer class="main-footer">
    {{-- Левый блок: Информация о правах и статус системы --}}
    <div class="footer-brand">
        <div class="footer-copy">
            © {{ date('Y') }} Все права защищены
        </div>
        <div class="footer-status">
            <span class="status-dot"></span>
            Система учета табелей • v1.0
        </div>
    </div>

    {{-- Правый блок: Карточка разработчика --}}
    <div class="footer-author">
        <div class="author-label">Инженерная разработка</div>
        <div class="author-name">Fedin Ruslan</div>
        <div class="author-contact">
            <a href="mailto:ruslan-fedin@yandex.ru" class="contact-link">
                ruslan-fedin@yandex.ru
            </a>
        </div>
    </div>
</footer>
