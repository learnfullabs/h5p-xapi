# H5P XAPI

## CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Bulk Operations
 * Related Modules
 * Maintainers


## INTRODUCTION
------------

This module will capture all the xAPI H5P Event Data statements per User and generates some statistics (in a view page) for public usage.

## PENDING

1. Implement code to save the H5P Resource's owner UID in the h5p_xapi_event_result table **DONE**

2. Implement a custom permission for the /h5p-xpi/event-result-data/ to give read permission only to resource owners and users who interacted with the resource **DONE**

3. Apply custom hook for changing some IDs to names/values in the /h5p-xpi/event-result-data/ view **DONE**
