#sMite

Simple mite

##Details

sMite is a PHP web app.

A user logs in using their mite API key.
(this key is currently just svaed in a cookie)

The user can then track time withing the app across multiple projects simultaneously.
Time is split equally between them.

The user can see how many seconds have been recorded for each project and then commit them to mite.

Note: the app contains simplified projects which currently come from a hardcoded map of projects and servvices contained within mite.

##TODO

 - Some sort of web framework should have been used...
 - Users should be able to stop a single timer rather than having to stop all timers at once.
 - The project list should be configurable per user (users can set up projects by creating a project -> service map for mite)
 - The mite endpoint should be configurable.
 - Docs page should be provided
 - OMG JavaScript (less page reloads duh)
 - A verbose log should be created of all actions for debugging things. (could also be visible to users that the log belongs to)
 - Seconds recoded should be in a table and also show mins / hours as well as seconds.
 - Saving notes should be optional / just turned off?
 - Users should be able to remove / adjust timers manually?
 - Users should be able to recover their recorded time if they change their API key? Login should perhaps also ask for the email address and validate that?
 - data directory should not be accessible to the wide wide world.