# OES Media Module
Welcome to the Open Encyclopedia System (OES) Media repository on GitHub.  
OES is a modular and configurable software platform for creating, publishing, and maintaining online encyclopedias in the humanities and social sciences. It is designed to be accessible worldwide through Open Access.

For more information, please visit the [main repository](https://github.com/open-encyclopedia-system) or our [website](https://open-encyclopedia-system.org).

A typical OES application consists of:
- the **OES Core plugin**
- a **project-specific OES plugin** that implements application-specific features
- optional **OES modules**, such as this module

The **OES Media** plugin provides reusable blocks and media-related functionality for use in OES-based projects.

⚠️ **Deprecation Notice**  
This module contains legacy code. As of **OES Core Plugin version 2.4.0**, its functionality has been replaced by the media blocks included in the core plugin.
We recommend using the latest version of **OES Core** for media-related features.

## Dependencies
This module depends on:

- **OES Core**, version `2.3.3`  
  Repository: [https://github.com/open-encyclopedia-system/oes-core](https://github.com/open-encyclopedia-system/oes-core)

- **Advanced Custom Fields (ACF Pro)**, version `6.3.4`  
  Website: [https://www.advancedcustomfields.com](https://www.advancedcustomfields.com)

## Support
This repository does **not** offer public support or issue tracking.  
If you need help using the OES plugins, please contact our help desk:  
**info@open-encyclopedia-system.org**

For information about available modules, customization options, or help launching your own encyclopedia, visit:  
[https://open-encyclopedia-system.org](https://open-encyclopedia-system.org)

## Documentation
The full user and technical manual is available at:  
[https://manual.open-encyclopedia-system.org/](https://manual.open-encyclopedia-system.org/)

## Contributing
If you are interested in contributing to OES development, please get in touch:  
**info@open-encyclopedia-system.org**

## Credits
Developed by **Digitale Infrastrukturen**, Freie Universität Berlin (FUB IT),  
with support from the **German Research Foundation (DFG)**.

## Licencing
Copyright (C) 2025
Freie Universität Berlin, FUB IT, Digitale Infrastrukturen
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

# Changelog

### 1.3.0
- Added: Blocks that work without ACF in OES Core

### 1.2.4
- Improved: Panel integration for OES Core 2.3.3
- Removed: `media.css` (moved to OES Core)

### 1.2.3
- Added: Prefix field for gallery titles
- Added: PDF display support

### 1.2.2
- Changed: Removed namespace from `\OES\Media\enqueue_scripts`
- Improved: Gallery block PDF display
- Fixed: Gallery slider bug

### 1.2.1
- Fixed: Gallery block behavior