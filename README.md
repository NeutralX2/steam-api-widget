# Steam-Api-Widget-Redux

A WordPress sidebar widget that displays a Steam user's profile — avatar, display name, online status, and account creation date — along with either the game they're currently playing or their most recently played games.

## Features

- Steam profile summary: avatar, persona name, online/away/in-game status, and account creation date
- When the configured user is in-game, shows a large header image for that game instead of the recently-played list
- Otherwise, shows a configurable number of recently played games, each with its icon
- Falls back to a placeholder icon if Steam doesn't have art for a given game (some titles' assets are incomplete on Steam's own CDN, even for legitimate, currently-released games)
- Data is refreshed in the background on a schedule (WP-Cron), not on every page load — a slow or unreachable Steam API never blocks or slows down page loads for visitors
- If Steam is temporarily unreachable, the widget keeps showing the last successfully fetched data instead of an error, until that data's own refresh interval lapses
- Multiple independent widget instances are supported (e.g. one per Steam account), each refreshed on its own schedule
- Inactive widget instances (configured but not placed in any sidebar) are skipped during background refresh, so they don't consume API quota for nothing

## Requirements

- A Steam Web API key: https://steamcommunity.com/dev/apikey
- The target account's Steam profile (and game details) privacy set to **Public** — Steam's API won't return data for a private profile
- The account's SteamID64 — https://steamidconverter.com/ can convert a profile URL or vanity name to this format if you don't already have it

## Installation

1. Upload the plugin folder to `wp-content/plugins/`
2. Activate it from the Plugins screen in wp-admin
3. Add the "Steam" widget to a sidebar from Appearance → Widgets
4. Configure it (see below) and save

## Widget settings

| Setting | Description |
|---|---|
| Title | The widget's heading text |
| API-Key | Your Steam Web API key |
| SteamID64 | The Steam account to display |
| Show # of games | How many recently played games to list when the user isn't currently in-game |
| Cache refresh interval (minutes) | How often this widget's data is refreshed from Steam in the background. Minimum is 1 minute — WordPress's own cron system can't reliably do better than that anyway, and there's no need for it to, since fetching is always background-only and never blocks a page load regardless of the interval. |

## How the caching works

Older versions of this plugin fetched from the Steam API directly during page rendering whenever the cache was empty or expired. During a Steam outage or slowdown, that meant every visitor's page load could hang waiting on the request to time out. Fetching now happens entirely in the background via WP-Cron: a scheduled job checks each configured, actively-placed widget instance on its own interval, refreshes it if due, and leaves the existing cached data untouched if a fetch fails. Widget rendering only ever reads from that cache — it never makes a live API call itself, so page loads are unaffected by Steam's availability.

## Development

This repo includes Prettier and EditorConfig for consistent PHP formatting on the class files. The `views/` templates are intentionally excluded from Prettier, since auto-formatting mangles their inline PHP-in-HTML.

```bash
npm install
```

Then run "Format Document" (or format-on-save) in your editor on the PHP files outside `views/`.

## Credits & License

Originally created by Armin Nowacki, with contributions from Faith999, and released under the GPLv2 (or later) license. The original plugin went unmaintained for a number of years; NeutralX2 has kept it patched since, with a number of reliability, security, and architecture improvements along the way — most recently a broader pass covering HTTPS/CDN fixes, output escaping, background-cron caching, and general code cleanup.

Released under GPLv2 or later, consistent with the original license.
