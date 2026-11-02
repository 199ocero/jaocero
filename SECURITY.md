# Security Policy

## Reporting a vulnerability

Please **do not** report security vulnerabilities through public GitHub issues, discussions or pull requests.

Report them privately through GitHub instead: go to the repository's **Security** tab and choose **Report a vulnerability** ([direct link](https://github.com/199ocero/jaocero/security/advisories/new)).

Please include:

- a description of the issue and its impact
- steps to reproduce, or a proof of concept
- the affected version or commit, if known

You can expect an acknowledgement within a few days. Once the issue is confirmed, a fix will be prepared and released, and you will be credited in the advisory unless you prefer otherwise.

## Supported versions

Only the latest commit on the `main` branch receives security fixes.

## Scope

In scope:

- the application code in this repository: the public site, the admin panel, the AI chat and its tools
- anything that exposes secrets (such as AI provider keys) to the browser, bypasses the chat or contact rate limits, gives admin access to emails outside `PORTFOLIO_ADMIN_EMAILS`, or lets the AI assistant read unpublished content
- injection through content rendered from markdown or from AI responses

Out of scope:

- vulnerabilities in third-party packages (please report them upstream)
- prompt injection that only makes the assistant say something off-topic, without exposing data or performing an action
- issues that require a compromised admin account or server
- forks and deployments run by other people

## For people running a fork

- Never commit your `.env`. It is in `.gitignore`.
- Keep `APP_DEBUG=false` in production.
- Keep `PORTFOLIO_ADMIN_EMAILS` limited to addresses you control.
- Set spending limits with your AI provider. The built-in rate limits reduce costs but are not a billing cap.
