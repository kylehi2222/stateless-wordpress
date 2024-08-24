=== Fluent Support Pro - WordPress Helpdesk and Customer Support Ticket Plugin ===
Contributors: techjewel, wpmanageninja, adreastrian
Tags: support, ticketing, fluent support
Requires at least: 5.6
Tested up to: 6.4
Stable tag: 1.7.90
Requires PHP: 7.3

Fluent Support Pro Version

== Installation ==
if you already have Fluent Support plugin then You have to install this plugin to get the additional features.

Installation Steps:
-----
1. Goto  Plugins » Add New
2. Then click on "Upload Plugins"
3. Then Click "Choose File" and then select the fluent-support-pro.zip file
4. Then Click, Install Now after that activate that.
5. You may need to activate the License, and You will get the license at your WPManageninja.com account.

Manual Install:
------------------------
Upload the plugin files to the /wp-content/plugins/ directory, then activate the plugin.

== Changelog ==

= 1.7.90 (Date: May 28, 2024) =
* Added - Activity Trends by Time of Day (Pro)
* Added - Integration with Fluent Boards (Pro)
* Added - Integrations Logs
* Added - Upload ticket attachments to their respective ticket folders in Google Drive, organized accordingly (Pro)
* Added - Duplicate or clone workflows (Pro)
* Added - Required option  in product field (Pro)
* Fixed - If the site language is not set to English, the workflow always defaults to manual mode
* Fixed - Inbox identifier css issue in all tickets table
* Fixed - If anyone choose View dashboard and draft_reply then it will not show any tickets
* Fixed - Freshdesk ticket migration issue
* Fixed - Zendesk ticket migration issue
* Fixed - Clicking the "Import Tickets" button in the ticket migration module opens multiple modals simultaneously
* Fixed - Issue with Bookmark
* Fixed - When the file name is too long, the file will not upload during ticket creation or in responses
* Fixed - If a restriction is applied to a specific business box, it still appears on the dashboard
* Fixed - MemberPress integration to show separate lists for recurring and non-recurring subscriptions
* Fixed - The WooCommerce widget is not shown on the 'View Customer' page

= 1.7.80 (Date: April 3, 2024) =
* Added - Restrict business boxes for specific agents
* Added - Ticket search feature in customer portal
* Added - MemberPress Integration
* Added - Option to resume the migration process for the last incomplete ticket in Helpscout (Pro)
* Added - Display the exact time of ticket or response creation upon hovering over it in the admin portal
* Fixed - Attachment download issue in email piping
* Fixed - BetterDocs integration issue
* Fixed - Agent Only field isn't displaying into the ticket
* Fixed - Draft Reply approve button issue with attachment
* Fixed - There is an issue with exporting the agent report time
* Fixed - The Gravatar image link is causing a PHP 8.2 deprecated issue
* Fixed - The issue with the "Enable Reply from Telegram" button in Telegram
* Fixed - The Auto Close Settings are not saving
* Fixed - Helpscout ticket migration issue
* Fixed - When responding to emails in German, create a new ticket instead of replying.

= 1.7.72 (Date: January 10, 2024) =
* Fixed - Notification integration settings issue
* Fixed - Displaying an incorrect assigned agent name
* Fixed - Links open in same tab issue
* Fixed - Telegram reply issue
* Fixed - Required functionality is not working in the conditional field
* Fixed - Ticket status issue

= 1.7.71 (Date: December 23, 2023) =
* Fixed - Email Piping Ticket Created Discord Notification Issue fixed

= 1.7.7 (Date: December 13, 2023) =
* Added - Trigger Fluent CRM automation within workflow (Pro)
* Added - Agent feedback in ticket response (Pro)
* Added - Agent permission for save response as draft
* Added - New shortcode for agent title signature in the inbox settings
* Added - Custom registration field using hooks
* Fixed - Agent can assign ticket without permission
* Fixed - The time duration displayed for ticket creation and response creation is inconsistent
* Fixed - Open a new thread in email for every response created
* Fixed - Translation issue
* MySQL orderby security issue fixed

= 1.7.6 (Date: November 07, 2023) =
* Improved File Upload
* Dropbox and Google Drive File Upload Issues Fixed
* Full Rewrite of the File Upload & Remote Driver System
* Improved UI

= 1.7.5 (Date: November 01, 2023) =
* Fixed - Ticket id not included in outgoing webhook
* Fixed - Update and delete issue in saved reply
* Fixed - Time difference issue in saved reply

= 1.7.4 (Date: October 31, 2023) =
* Fixed - Freshdesk migrator issue
* Fixed - Added a few missing translations
* Fixed - Summary report issue fixing for products and business inbox
* Fixed - File upload and view issue for 3rd party

= 1.7.3 (Date: August 23, 2023) =
* Added - Report by Product(Pro)
* Added - Report by Business Inbox(Pro)
* Fixed - Create ticket issue for required fields is fixed
* Fixed - Custom field not showing in the add field from
* Fixed - Added missing translations

= 1.7.2 (Date: July 17, 2023) =
* Fixed - Create ticket issue for required fields is fixed
* Fixed - Custom field not showing in the add field from

= 1.7.0 (Date: July 14, 2023) =
* Added - Support email cc
* Added - Option to set dedicated mailbox for webhook
* Added - Business box added in the workflow action and condition list
* Added - Support file attachment upload in third party (Google Drive and Dropbox)
* Added - Zendesk migrator
* Fixed - Work action ordering issue
* Fixed - Custom field required in conditional form
* Fixed - Conditional form rendering issue
* Fixed - Ticket create using API endpoint
* Fixed - Freshdesk migrator issue

= 1.6.9 (Date: March 16, 2023) =
* Added - Custom field required or optional
* Added - Custom field in the workflow condition
* Added - Saved replies templates in auto ticket close module
* Added - Saved replies templates in the workflow
* Fixed - Fluent CRM widget missing issue
* Fixed - Ticket merge popup issue
* Fixed - Delete action of manual workflow
* More improvements

= 1.6.8 (Date: February 14, 2023) =
* Added - Migrate Tickets from Freshdesk
* Added - Toggle to stop auto close bookmarked tickets
* Fixed - Issue with telegram reply
* Fixed - Support staff not assigned to ticket via workflow
* Fixed - Frontend agent portal issues
* More Bug Fixes and Improvements

= 1.6.7 (Date: November 24, 2022) =
* Agent Summary Exporter
* Migrate Tickets from Help Scout
* WooCommerce Purchase History Widget Redesigned
* Bug Fixes and Improvements

= 1.6.6 (Date: October, 2022) =
* Activity Log Filters
* Active Tickets for Products
* Waiting Ticket stat on Dashboard
* Hourly Reports for tickets activity
* New Trigger – Ticket Closed on Automation
* Close Ticket Silently (without triggering emails)
* Migrate Tickets from Awesome Support
* Migrate Tickets from SupportCandy
* Bug Fixes and Improvements

= 1.6.5 (Date: August 24, 2022) =
* Added Auto Close Ticket Module based on ticket inactivity days
* Improved Saved Replies. Now you can add more replies
* Fixed File Upload Issues
* Fixed Few minor issues on integrations

= 1.6.2 (Date: August 22, 2022) =
* Fixed fiw minor bugs regarding data sanitizations
* Saved Replied issues Fixed
* All external links are will open in new tab
* Auto Linking linkable contents
* Create new ticket flow improved

= 1.6.0 (Date: August 19, 2022) =
* NEW - Agent portal in frontend
* Added - Shortcode support in workflow
* Added - LearnPress integration
* Added - Split reply to a new ticket
* Added - License status in EDD widget
* Added - Ticket closing feature from Slack and Telegram
* Added - Adding or removing ticket bookmark from workflow
* Improvement - Security
* Improvement - Code Base

= 1.5.7 (Date: July 07, 2022) =
* Added - Global Customer Searching on Ticket Creation on Behalf of Customer
* Added - Template for Ticket Creation on Behalf of Customer
* Fixed - WooCommerce Order Total Issue
* Fixed - Text Encoding Issue on Email Piping

= 1.5.6 (Date: May 26, 2022) =
* Added - Ticket Merge System
* Added - Ticket Watcher System
* Added - Mailbox Check in Workflow
* Added - FluentCRM List & Tag Check in Workflow
* Added - FluentCRM List & Tag Attach & Detach in Workflow
* Fixed - WooCommerce Multi Currency Issue
* Fixed - WooCommerce Draft Product Display in Custom Fields

= 1.5.5 (Date: March 02, 2022) =
* Added - Whatsapp integration via twilio
* Added - Outgoing Webhook Integration in workflow
* Added - Agents report filtering by specific agent
* Added - Today's stats of tickets
* Added - Send notification to 3rd party integrated notification system on agent assign
* Added - Ticket moving feature from one mailbox to another
* Fixed - Ticket created email notification not sending when creating a ticket via incoming webhook

= 1.5.4 (Date: January 19, 2022) =
* Added - Ticket advanced filtering
* Added - Custom fields on Telegram integration
* Added - Incoming Webhook
* Added - Missing translations
* Fixed - Issues related to email piping
* Fixed - Email footer not sending to email notification
* Fixed - Discord Notification issues
* Fixed - Custom fields not saving when creating a ticket from agent dashboard
