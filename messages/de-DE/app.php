<?php

$lang = [
    'Update' => 'aktualisieren',
    'Delete' => 'löschen',
    'Save' => 'speichern',
    'Create' => 'erstellen',
    'Created At' => 'Erstellungszeitpunkt',
    'Updated At' => 'Aktualisierungszeitpunkt',
    'Action' => 'Aktion',
    'Are you sure you want to delete this item?' => 'Wollen Sie das Elemente wirklich löschen?',
    'Are you sure you want to restore this item?' => 'Sind Sie sicher diesen Datensatz in der Datenbank wiederherzustellen?',
    
    'Print Overview (PDF)' => 'Übersicht drucken (PDF)',

    // Lehrer 
    'Teachers' => 'Lehrer',
    'Create Teacher' => 'Lehrer erstellen',
    'Inital' => 'Initialien',
    'Name' => 'Name',
    'Firstname' => 'Vorname',
    'Email 1' => 'Email',
    'Email 2' => 'Email 2',
    'Phone' => 'Telefon',
    'Mobile' => 'Mobilnummer',
    'Update Teacher: ' => 'Lehrer aktualisieren:',

    // Lehrer Favoriten
    'Teacher Fav' => 'Lehrer-Favoriten',
    'Teachers Fav' => 'Lehrer-Favoriten',
    'Create Teacher Fav' => 'Lehrer-Favoriten erstellen',

    // Abteilungen
    'Department' => 'Abteilung',
    'Departments' => 'Abteilungen',
    'Create Department' => 'Abteilung erstellen',
    'Head Of Department' => 'Abteilungsleiter',
    'Default Color' => 'Standardfarbe',
    'Update Department:' => 'Abteilung aktualisieren:',

    // Schulklassen
    'School Class' => 'Schulklasse',
    'School Classes' => 'Schulklassen',
    'Create School Class' => 'Schulklasse erstellen',
    'Classname' => 'Klassenname',
    'Annual Value' => 'Jahreswert',
    'Class Head' => 'Klassenvorstand',
    'Studentsnumber' => 'Schüleranzahl',

    // Fach
    'Subject' => 'Fach',
    'Subjects' => 'Fächer',
    'Create Subject' => 'Fach erstellen',
    'subject_value' => 'Wertigkeit',
    'sortorder' => 'Reihenfolge',

    // Fächerzuteilng
    'Class Subject' => 'Fächerzuweisung',
    'Class Subjects' => 'Fächerzuweisungen',
    'Update Class Subject:' => 'Update Zuweisung:',
    'Update Class Subject: {name}' => 'Update Zuweisung:',
    'Class' => 'Schulklasse',
    'Group' => 'Gruppe',
    'Value' => 'Prozent',
    'Hours' => 'Stunden',
    'Classroom' => 'Raum',

    // Report
    'Report.TeacherByClass' => 'Klassenlehrer',
    'Report.TeacherWorkload' => 'Lehrerauslastung',
    'Report.TeacherWorkloadPerDepartment' => 'Lehreauslastung je Abteilung',

];

// load custom language-infos to overwrite default
return array_unique(array_merge($lang,require_once("custom.php")));