# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability in CoreWatch, please **do not** open a public GitHub issue.

Email **hamdyelbatal122@hamzi.dev** with:

- A description of the vulnerability
- Steps to reproduce
- Potential impact assessment

We aim to respond within 48 hours and will coordinate a fix and disclosure timeline with you.

## Security Design Principles

1. **No arbitrary shell execution** — only whitelisted command keys from `config/corewatch.php`
2. **Environment gating** — restrict dashboard access per environment
3. **Middleware-first auth** — require `auth` middleware in production
4. **Read-only log streaming** — log viewer never writes to files
5. **Memory-safe parsing** — chunked backward reads with bounded memory
