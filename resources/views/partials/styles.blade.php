<style>
    :root, html.light {
        --cw-bg: #f1f5f9;
        --cw-bg-gradient: radial-gradient(circle at 50% -20%, #e0f2fe 0%, #f1f5f9 55%);
        --cw-surface: #ffffff;
        --cw-card: #ffffff;
        --cw-inset: #f8fafc;
        --cw-border: #e2e8f0;
        --cw-border-strong: #cbd5e1;
        --cw-text: #0f172a;
        --cw-text-secondary: #334155;
        --cw-muted: #64748b;
        --cw-accent: #0284c7;
        --cw-accent-soft: rgba(2, 132, 199, 0.1);
        --cw-success: #059669;
        --cw-success-soft: rgba(5, 150, 105, 0.1);
        --cw-warning: #d97706;
        --cw-warning-soft: rgba(217, 119, 6, 0.1);
        --cw-danger: #dc2626;
        --cw-danger-soft: rgba(220, 38, 38, 0.1);
        --cw-purple: #7c3aed;
        --cw-terminal: #0f172a;
        --cw-terminal-bar: #1e293b;
        --cw-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 4px 16px rgba(15, 23, 42, 0.04);
        --cw-shadow-lg: 0 8px 30px rgba(15, 23, 42, 0.1);
        --cw-scrollbar-track: #f1f5f9;
        --cw-scrollbar-thumb: #cbd5e1;
    }

    html.dark {
        --cw-bg: #030712;
        --cw-bg-gradient: radial-gradient(circle at 50% 0%, #0d1e3d 0%, #050b18 100%);
        --cw-surface: #050b18;
        --cw-card: #0c1528;
        --cw-inset: #050b18;
        --cw-border: #1f2e4d;
        --cw-border-strong: #2d4070;
        --cw-text: #f1f5f9;
        --cw-text-secondary: #cbd5e1;
        --cw-muted: #94a3b8;
        --cw-accent: #00ccff;
        --cw-accent-soft: rgba(0, 204, 255, 0.12);
        --cw-success: #00ff88;
        --cw-success-soft: rgba(0, 255, 136, 0.1);
        --cw-warning: #ff9100;
        --cw-warning-soft: rgba(255, 145, 0, 0.1);
        --cw-danger: #ff3366;
        --cw-danger-soft: rgba(255, 51, 102, 0.1);
        --cw-purple: #ab47bc;
        --cw-terminal: #000000;
        --cw-terminal-bar: #0c121e;
        --cw-shadow: 0 0 15px rgba(0, 204, 255, 0.05);
        --cw-shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.4);
        --cw-scrollbar-track: #0c1528;
        --cw-scrollbar-thumb: #1f2e4d;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--cw-bg-gradient);
        background-color: var(--cw-bg);
        color: var(--cw-text);
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    .code-font { font-family: 'JetBrains Mono', monospace; }

    .cw-page { max-width: 80rem; margin: 0 auto; padding: 1rem 1.5rem 2rem; }
    @media (min-width: 1024px) { .cw-page { padding: 1.5rem 2rem 2.5rem; } }

    .cw-section { margin-bottom: 1.5rem; }
    .cw-section-title {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--cw-muted);
        margin-bottom: 0.75rem;
        padding-left: 0.25rem;
    }

    .cw-card {
        background: var(--cw-card);
        border: 1px solid var(--cw-border);
        border-radius: 0.875rem;
        padding: 1.25rem;
        box-shadow: var(--cw-shadow);
        transition: border-color 0.2s, box-shadow 0.2s, background-color 0.25s;
    }
    .cw-card:hover { box-shadow: var(--cw-shadow-lg); }
    .cw-card.cw-card-alert { border-color: var(--cw-danger); box-shadow: 0 0 0 1px var(--cw-danger-soft); }

    .cw-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--cw-border);
    }

    .cw-card-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--cw-text-secondary);
    }

    .cw-dot { width: 0.5rem; height: 0.5rem; border-radius: 9999px; flex-shrink: 0; }
    .cw-dot-pulse { animation: cw-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes cw-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    .cw-badge {
        font-size: 0.65rem;
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid var(--cw-border);
        background: var(--cw-inset);
        color: var(--cw-muted);
        white-space: nowrap;
    }
    .cw-badge-accent { color: var(--cw-accent); border-color: var(--cw-accent-soft); background: var(--cw-accent-soft); }
    .cw-badge-success { color: var(--cw-success); border-color: var(--cw-success-soft); background: var(--cw-success-soft); }
    .cw-badge-danger { color: var(--cw-danger); border-color: var(--cw-danger-soft); background: var(--cw-danger-soft); }
    .cw-badge-warning { color: var(--cw-warning); border-color: var(--cw-warning-soft); background: var(--cw-warning-soft); }

    .cw-progress {
        height: 4px;
        background: var(--cw-inset);
        border-radius: 9999px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .cw-progress-bar {
        height: 100%;
        border-radius: 9999px;
        background: linear-gradient(90deg, var(--cw-accent), var(--cw-success));
        transition: width 0.6s ease;
    }
    .cw-progress-bar.warn { background: linear-gradient(90deg, var(--cw-warning), var(--cw-danger)); }
    .cw-progress-bar.danger { background: var(--cw-danger); }

    .cw-table { width: 100%; font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; }
    .cw-table td { padding: 0.6rem 0; vertical-align: middle; }
    .cw-table tr + tr td { border-top: 1px solid var(--cw-border); }
    .cw-table tr:hover td { background: var(--cw-inset); }
    .cw-table .cw-label { color: var(--cw-muted); }
    .cw-table .cw-value { text-align: right; color: var(--cw-text); font-weight: 600; }

    .cw-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        padding: 0.5rem 0.875rem;
        border-radius: 0.5rem;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .cw-btn-primary { background: var(--cw-accent); color: #030712; }
    html.light .cw-btn-primary { color: #fff; }
    .cw-btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .cw-btn-ghost {
        background: var(--cw-inset);
        border-color: var(--cw-border);
        color: var(--cw-text-secondary);
    }
    .cw-btn-ghost:hover { border-color: var(--cw-accent); color: var(--cw-accent); }
    .cw-btn-sm { padding: 0.3rem 0.65rem; font-size: 0.65rem; }

    .cw-input, .cw-select {
        width: 100%;
        background: var(--cw-inset);
        border: 1px solid var(--cw-border);
        border-radius: 0.5rem;
        padding: 0.45rem 0.65rem;
        font-size: 0.75rem;
        font-family: 'JetBrains Mono', monospace;
        color: var(--cw-text);
        transition: border-color 0.15s;
    }
    .cw-input:focus, .cw-select:focus { outline: none; border-color: var(--cw-accent); }
    .cw-input::placeholder { color: var(--cw-muted); opacity: 0.7; }

    .cw-theme-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem;
        border-radius: 0.625rem;
        border: 1px solid var(--cw-border);
        background: var(--cw-inset);
    }
    .cw-theme-btn {
        padding: 0.35rem 0.65rem;
        border-radius: 0.4rem;
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--cw-muted);
        transition: all 0.15s;
        cursor: pointer;
        border: none;
        background: transparent;
    }
    .cw-theme-btn.active {
        background: var(--cw-card);
        color: var(--cw-accent);
        box-shadow: var(--cw-shadow);
    }

    .cw-topbar {
        height: 3px;
        background: linear-gradient(90deg, var(--cw-accent), var(--cw-success), var(--cw-danger));
    }

    .cw-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        border: 1px solid var(--cw-border);
        background: var(--cw-inset);
        color: var(--cw-text-secondary);
    }

    .cw-terminal {
        background: var(--cw-terminal);
        border: 1px solid var(--cw-border);
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .cw-terminal-bar {
        background: var(--cw-terminal-bar);
        border-bottom: 1px solid var(--cw-border);
        padding: 0.6rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.65rem;
        font-family: 'JetBrains Mono', monospace;
        color: var(--cw-muted);
    }

    .text-success { color: var(--cw-success) !important; }
    .text-warning { color: var(--cw-warning) !important; }
    .text-danger { color: var(--cw-danger) !important; }
    .text-accent { color: var(--cw-accent) !important; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--cw-scrollbar-track); }
    ::-webkit-scrollbar-thumb { background: var(--cw-scrollbar-thumb); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--cw-accent); }
</style>
