# fruechtekorb
This is a very simple text corpus management system for the german linguistic department of the university of Basel.

## What it does:
- It gives you the possibility to create, alter and delete documents in a database
- There is a process to ask authors of a text for their permission to use the text in a corpus. The author can add metadata.
- With a simple markdown editor, a user of the system can add the text. It is saved as TEI-XML.
- The corpus can be searched, the results can be filtered by metadata (feature completion in progress).

## What it contains:
The project consists of the following components:
- PHP scripts make up the browser based management UI
- A database scheme to store created documents
- Some sample TEI-XML documents to show how the data is stored.

## How to use it:
- Create a MySQL database with the scheme 'korpus.sql'
- Change the credentials in db.php to connect to your database
- Load the project into a webservers 'htdocs' directory.
