# Open Encyclopedia System Plugin

Welcome to the Open Encyclopedia System Media (OES Media) repository on GitHub. OES is a modular and configurable software for creating, publishing and maintaining online encyclopedias in the humanities and social sciences that are accessible worldwide via Open Access. For more information please visit the main repository or our website.

A typical OES application consists of an OES plugin and an additional OES project plugin which implements the OES features for the application. The OES Media plugin is an additional OES module to include media blocks and functionalities.

## Documentation (Coming Soon)

We are working on a detailed user manual and a technical function reference. Support is currently provided via our email help desk info@open-encyclopedia-system.org. We answer questions related to the OES plugin and its usage.


## Getting Started

### Dependencies

OES Media depends on the OES Core Plugin:
* OES Core, version 2.3.3, URL: https://github.com/open-encyclopedia-system/oes-core.

OES Media depends on the WordPress plugin ACF PRO:
* Advanced Custom Fields, version 6.3.4, URL: https://www.advancedcustomfields.com/pro/.


### Installation

1. Download the OES plugin from gitHub and add it to your WordPress plugin directory.
2. Download and activate the dependencies.
3. Activate the OES plugin.
4. Create your OES project plugin or download and activate the OES Demo plugin.
5. (Optional) Download and activate the OES theme.
6. Download and activate the OES Media module.

If the installation was successful you will now see the menu "OES Settings" and when using the block editor you can select the blocks "OES Panel", "OES Image Panel" and "OES Gallery Panel". The OES Media module and its functionalities are now available inside your WordPress installation.

You can start configuring by exploring the OES settings (documentation coming soon) or editing the model.json inside your project plugin.

## Support

This repository is not suitable for support.

Support is currently provided via our email help desk info@open-encyclopedia-system.org. We answer questions related to the OES plugin and its usage. For further information about online encyclopedias and possible customization please visit our [website](http://www.open-encyclopedia-system.org/).


## Contributing

If you want to contribute to OES please contact the help desk info@open-encyclopedia-system.org.


## Licencing

Copyright (C) 2023 Freie Universität Berlin, Center für Digitale Systeme an der Universitätsbibliothek

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

# Changelog

## 1.2.4
* improve - panel calls to fit OES Core 2.3.3
* remove - media.css, moved to OES Core

## 1.2.3
* add - prefix field for gallery title in gallery block
* add - pdf display

## 1.2.2
* change - remove namespace für \OES\Media\enqueue_scripts
* improve - pdf display for gallery block
* fix - gallery slider

## 1.2.1
* fix - gallery block