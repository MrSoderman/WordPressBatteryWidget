# 🔋 Unicode Battery Widget for WordPress

A lightweight WordPress widget that displays your device’s battery level using Unicode block characters (▰▱) and an emoji (⚡ when charging).  
It updates live using the browser’s `navigator.getBattery()` API.

---

## ✨ Features
- Live battery percentage and charging indicator  
- Adjustable number of Unicode blocks (1–20)  
- Custom font size and charging emoji  
- Clean, minimal, and lightweight — no external dependencies  
- Fully compatible with modern WordPress (6.x / PHP 8.x+)  

---

## ⚙️ Installation

1. Upload the plugin folder **`battery-widget`** (containing `battery-widget.php`) to your WordPress directory: /wp-content/plugins/battery-widget/

2. Go to **Dashboard → Plugins** and activate **Battery Widget**.  
3. Configure settings under **Settings → Battery Widget** (blocks, font size, emoji).  
4. Add the widget **Unicode Battery** via **Appearance → Widgets** to any sidebar or footer.  

---

## 🧠 Notes

- Uses the modern **Battery Status API** (`navigator.getBattery()`).
- If the API isn’t supported (e.g., some Safari or desktop browsers), the widget displays `[?] N/A`.
- Safe to deactivate/reactivate — settings are stored in WordPress options (`get_option()` / `update_option()`).
- No caching, buffering, or core file changes — 100% self-contained and safe for live use.

---

## 🧑‍💻 Author
**Mr Soderman**  
Version: **1.1**  
License: **GPLv2 or later**

---

