# Demo WordPress Plugin for Working with External API

## Description

This plugin allows you to fetch data from an external source and display it on a page in the admin panel or output the data using a shortcode.

## Installation

Run `sh setup.sh`

## Usage Instructions

- View the records on the page: `http://example.com/wp-admin/admin.php?page=tasks-sid`
- Update the records by clicking the "Refresh" link in the top left corner of the screen.

Use the following shortcode to display the records in random order on the site pages:
`[sid_tasks]`.

By default, 5 records are displayed. You can change this value by passing the `count` parameter.
Example: `[sid_tasks count=5]`