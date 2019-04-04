Das Modul (ivm_immo_collection), das ich geschrieben habe, enthält ein Frontend-Modul für die Ausgabe der Merkliste und eine Klasse zur Generierung des Star-Buttons über den Inserttag {{ivmImmoCollectionToggleCollection}}. 

Hier sind die Templates, die ich angefasst habe.
 
Collection Template (Merkliste):
system\modules\ivm_immo_collection\templates\mod_immosearch_listcollection.html5
 
Button Template zur Generierung des Inserttags:
system\modules\ivm_immo_collection\templates\toggleCollection.html5

Detail Template:
templates\mod_immosearch_listresultdetail.html5

Listentemplate
templates\mod_immosearch_listresult.html5

So verwendest du den Inserttag !Achtung der 2. Parameter ist optional und wird auf der Merkliste gebraucht.:


Inserttag:
{{ivmImmoCollectionToggleCollection::[WohnungsId]::[Optional: css selector des übergeordneten Elements, das bei unfeature aus dem DOM entfernt werden soll. (Siehe Collection Liste)]}}

Beispiel im Listen- und Detailtemplate:
{{ivmImmoCollectionToggleCollection::51}}

Oder mit optionalem 2. Parameter un der Merkliste:
{{ivmImmoCollectionToggleCollection::51::div.el_item}}




