# YOURLS Reverse Proxy Support (Fork by Darkhorse)

This is a fork and enhancement of Diftraku's original [YOURLS Cloudflare plugin](https://github.com/Diftraku/yourls_cloudflare/). While retaining the core functionality of correctly identifying client IPs behind reverse proxies, this version introduces **IP anonymization** for enhanced privacy.

This plugin for YOURLS fixes the reported user IPs to show the actual client IP address, rather than the IP of a reverse proxy or cloud provider's infrastructure. This ensures more accurate logging and statistics within YOURLS, with an added layer of privacy.

The plugin identifies the real IP from common HTTP headers provided by reverse proxies, then sanitizes and anonymizes it before applying this information as a filter to `yourls_get_IP()`.

### Key Enhancements in this Fork:

*   **IP Anonymization:** Automatically masks the last octet of IPv4 addresses and the last 4 hextets of IPv6 addresses. This helps in meeting privacy requirements (e.g., GDPR) by reducing the granularity of stored IP data while still allowing for general geographic or network analysis.
*   **Improved `X-Forwarded-For` Handling:** Explicitly takes the first IP in the `X-Forwarded-For` chain, which is typically the client's original IP, commonly used by various proxies like Heroku or Nginx.
*   **Enhanced Header Checks:** Ensures that proxy headers are not only set (`isset`) but also not empty (`!empty`) before use.

---

## Why Use This Plugin?

When a reverse proxy (like Cloudflare or Heroku's router infrastructure) sits between your YOURLS instance and the internet, your server typically sees the proxy's IP address instead of the original client's IP. This plugin ensures that YOURLS logs the correct, real IP of the visitor. With the added anonymization, you can maintain better user privacy.

Currently supported headers for real IP detection include:

*   **`CF-Connecting-IP`**: Used by Cloudflare.
*   **`X-Forwarded-For`**: Commonly used by various proxies, including Heroku, Nginx, and other load balancers.
*   **`X-Real-IP`**: Also frequently used by proxies such as Nginx or some load balancers.

## Installation

1.  **Download:** Fetch or download this plugin's repository.
2.  **Copy:** Copy the plugin folder (e.g., `reverseproxy`) into your YOURLS plugins directory: `YOURLS_ABSPATH/user/plugins/`, where `YOURLS_ABSPATH` is the root of your YOURLS installation.
3.  **Activate:** Log into your YOURLS administration interface and activate the "Reverse Proxy Support" plugin.

---

### References:

*   [Original Cloudflare Plugin by Diftraku](https://github.com/Diftraku/yourls_cloudflare/)
*   [Cloudflare - Restoring original visitor IPs](https://support.cloudflare.com/hc/en-us/articles/200170786-Restoring-original-visitor-IPs-Logging-visitor-IP-addresses-with-mod-cloudflare-)
*   [Heroku - HTTP Routing (Heroku Headers)](https://devcenter.heroku.com/articles/http-routing#heroku-headers)
