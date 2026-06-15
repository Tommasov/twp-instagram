# TWP - InstaFlow Connect

**TWP - InstaFlow Connect** is a lightweight and elegant WordPress plugin that lets you easily integrate your Instagram feed directly into your website. It supports static images, carousels, videos and Reels.

## 🚀 Features

- **Simple Integration**: Display your feed with a handy shortcode.
- **Multi-Format Support**: Correctly handles images, carousels and videos/Reels.
- **Optimized Performance**: Local caching of images and data to reduce API calls and speed up page loading.
- **API Flexibility**: Supports both the Instagram API with Instagram Login (regular accounts) and the Graph API for Business accounts.
- **Webhook Ready**: Includes an endpoint to handle real-time notifications from Meta.
- **Modern Design**: Ready-to-use responsive grid.
- **Translation Ready**: English by default, with a bundled Italian translation (`it_IT`).

## 📦 Installation

1. Download the plugin folder.
2. Upload the `twp-instagram-endpoint` folder to the `/wp-content/plugins/` directory of your WordPress site.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to **InstaFlow Connect > Settings** to configure your Meta APIs.

## 🛠 Configuration

To make the plugin work, you need to create an App on the [Meta for Developers](https://developers.facebook.com/) portal:

1. **Account Type**: Choose between "Regular account" or "Business" depending on your needs.
   - **Regular account**: uses the **Instagram API with Instagram Login** (which replaced the deprecated *Basic Display API* on December 4, 2024). In your App, add the **Instagram** product and open the **API setup with Instagram login** section: from there copy the **Instagram App ID** and **Instagram App Secret**, and add the redirect URL shown by the plugin to the **OAuth Redirect URIs**. The Instagram account must be **Professional** (Business or Creator).
   - **Business**: uses **Facebook Login** with the Graph API and supports Webhooks.
2. **App ID & Secret**: Enter the credentials provided by Meta.
3. **Webhook (Business only)**:
   - Go to your Meta App.
   - Add the **Instagram Graph API** product.
   - Go to **Webhook**, select **Instagram** from the dropdown menu.
   - Click **Configure this webhook**.
   - Enter the **Callback URL** and the **Verify Token** found on the plugin settings page.
   - Subscribe to the `media` objects to receive updates about new posts.

## 📝 Shortcode Usage

You can insert the Instagram grid into any page, post or widget using the shortcode:

### Basic
```text
[twp_instagram_grid]
```
*Displays the latest 9 posts (images/carousels).*

### With Parameters
You can customize the display with the following attributes:

- `count`: Number of posts to show (default: `9`).
- `video`: Set `yes` to also include Videos and Reels (default: `no`).

**Examples:**
- Show 12 posts including videos: `[twp_instagram_grid count="12" video="yes"]`
- Show only 3 posts: `[twp_instagram_grid count="3"]`

## 🌐 Translations

The plugin ships in English by default and includes an Italian translation, automatically loaded when WordPress is set to Italian (`it_IT`).

Translation files live in the `languages/` folder:
- `instaflow-connect.pot` — template for new translations.
- `instaflow-connect-it_IT.po` / `.mo` — Italian translation (editable source / compiled binary).

To regenerate the compiled files after editing translations, run `php languages/build-translations.php` (or `wp i18n make-mo languages/` if you use WP-CLI).

## 🔒 Privacy & Compliance

The plugin is designed to comply with Meta's policies. A pre-generated `privacy-policy.html` file is included, which you can customize and upload to your site to provide the required information to users along with instructions for data deletion.

---
**Author:** [Tommaso Vietina](https://www.tommasovietina.it/)
**Version:** 1.9