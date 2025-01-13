# Nigerian News Aggregator
A WordPress plugin that aggregates headlines from major Nigerian gossip and news blogs, providing a centralized hub for staying updated with Nigerian online media.
Features

* Automatic aggregation of headlines from popular Nigerian news sources
* Customizable news categories and sources
* Clean and responsive design that integrates seamlessly with WordPress themes
* Regular updates to ensure fresh content
* Easy-to-use admin interface for managing news sources and display settings

## Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New
4. Click on "Upload Plugin"
5. Choose the downloaded zip file and click "Install Now"
6. After installation, click "Activate" to enable the plugin

## Configuration
### Adding News Sources

1. Go to WordPress Admin → Nigerian News Aggregator → Sources
2. Click "Add New Source"
3. Enter the required information:

* Source Name
* Website URL
* RSS Feed URL (if available)
* Categories


4. Click "Save" to add the source

## Display Settings

1. Navigate to WordPress Admin → Nigerian News Aggregator → Settings
2. Configure the following options:

* Number of headlines to display
* Update frequency
* Display format
* Category filters


3. Save your changes

## Usage
### Shortcode
Use the following shortcode to display the news feed on any page or post:
`[nigerian_news_feed]`  

Optional parameters:  

`[nigerian_news_feed category="politics" count="10" source="punch"]`
## Widget

1. Go to Appearance → Widgets
2. Find "Nigerian News Feed" widget
3. Drag it to your desired widget area
4. Configure the widget settings
5. Save changes

## Requirements

* WordPress 5.0 or higher
* PHP 7.2 or higher
* Active internet connection for fetching news updates

## Support
For support, bug reports, and feature requests, please:

1. Create an issue on our GitHub repository
2. Provide detailed information about your WordPress environment
3. Include steps to reproduce any bugs you encounter

## Contributing
We welcome contributions! To contribute:

1. Fork the repository
2. Create a new branch for your feature
3. Commit your changes
4. Create a pull request
5. Describe your changes in detail

Please ensure your code follows WordPress coding standards.
## License
This plugin is licensed under the GPL v2 or later.
## Credits
Developed by Ope Adedotun
## Changelog
### 1.0.0

* Initial release
* Basic news aggregation functionality
* Admin interface for source management
* Shortcode and widget support
