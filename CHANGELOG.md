## 1.1.0

A massive release containing unimaginable amount of new features, fixes and actually the first version
of Agile Data that allows you to actually build nice apps with it.

### Major Features
- Added CRUD component to add, edit, delete and add records to your data sets #105, 
- Added Advanced Grid now supporting checkbox column, actions, multiple formatters, anonymous columns, and delete
- .. also Renamed old Grid into Table, #118 #84 #83 #93 #95 #64
- Added QuickSearch #107 
- Added Paginator
- Added Form Model, Validation support
- Added Form Fields: Textarea, Dropdown
- Added Automated multi-column layout FormLayout\Columns
- Added support for stickyGet #131
- Added jsModal() for dialogs #124 #71
- Added jsReload() with argument support and spinner #51 #66 #78 #79 
- Added Message #100
- Added Label #88
- Added Columns #65
- Added JS Library #73
- Form can edit all field types of from Agile Data
- Renamed Grid into Table

### New Demo Pages
 - Layouts #123 #113
 - Form / Multi-column layout
 - Grid / Table Interactions
 - Grid / Table+Bar+Search+Paginator
 - Grid / Interactivity - Modals
 - Crud
 - View demo #104
 - Message
 - Labels
 - Menu #96 #97 
 - Paginator
 - Interactivity / Element Reloading
 - Interactivity / Modal Dialogs
 - Interactivity / Sticky GET
 - Interactivity / Recursive Views

### Fixes
- Bugfixes #111, #86, #85

### Minor changes
- Upgraded to Agile Core 1.2 #129
- Field->jsInput()
- App->requireJS() #120 #50
- Remaned all .jade files into .pug #89
- Renamed namespace Column into TableColumn

Full diff: https://github.com/atk4/ui/compare/1.0.3...1.1.0

## 1.0.2

* Button::$rightIcon renamed into $iconRight to follow global pattern
* Removed depreciated classes H2, Fields and MiniApp
* Cleaned up demos/button.php
* Added documentation for Button class
* Refactored Button internals (simplified), now uses button.html
* Added comments for a Form
* Cleaned up Grid type-hinting
* Added example for top/bottom attached buttons to Grid.
* You can disable "header" for grid now

## 1.0.1

Qucik post-release bugfixes

## 1.0.0

* Implement Grid
* Many improvements everywhere
* Simpler execution
* First stable release

## 0.4

* Implemented Layouts (Admin / Centered) #33
* Created nicer demos

## 0.3

* Implemented js() and on() #20
* Implemented Server-Side JS calls #28
* Implemented Form #29 and #30
* Enhanced documentation

## 0.2

* Implemented Render Tree
* Implemented Template-based Rendering #15
* Implemented Basic View #16
* Implemented Button (based around Semantic UI)
* Implemented JavaScript events
* Advanced JSChains (enclosing, etc) #18
* Implemented Very Basic Layouts

## 0.1

* Initial Release
* Bootstraped Documentation (sphinx-doc)
* Implemented CI
