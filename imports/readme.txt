Instructions for importing volunteers from a spreadsheet into VMAT:
1) install and activate the import-users-from-csv-with-meta plugin https://wordpress.org/plugins/import-users-from-csv-with-meta/.
2) Go to VMAT settings and select "Enable Volunteer Imports". This will allow the VMAT plugin to plug into the appropriate hooks in the import-users-from-csv-with-meta plugin so we can associate the imported volunteer with an initial event. To make this work the spreadsheet needs a column named _vmat_initial_event_id with a valu set to the post_id of an existing published event.
3) fillout a spreadsheet using the format of the example spreadsheet "example_volunteer_import.csv" found in the imports directory of the VMAT plugin directory.
4) Go to the WordPress dashboard Tools->"import and export users and customers" page
5) On the "Import" tab, chjoose the previously created csv file
6) Select a default role of Volunteer
7) Make sure both checkboxes under "Send Mail" are unselected. This is important to avoid having the volunteers get an email.
8) Select "Update Existing Users" Yes
9) Select "Update Roles for Existing Users" No (If you have a "roles" column in your spreadsheet you can provide a comma separated list of user roles to assign to thye user. In that case you may want to override existing roles. Just be careful!)
10) Un-check "Delete users that are not present in CSV"
11) Un-check "Change role of users that are not in csv"
12) Click "Start Importing"
13) This may take some time depending on how large the spreadsheet is. When it is done, you will see a status screen, which may contain warnings or errors.
14) Go to the VMAT plugin Manage Volunteers tab to see if things were successfully imported.
15) When finished, go to VNAT Settings and unhselect "Enable Volunteer Imports"
16) deactivate the import-users-from-csv-with-meta plugin
