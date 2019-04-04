# IVM Immo Collection (Merklistenmodul)
Das Contao Modul (ivm_immo_collection) enthält ein Frontend-Modul für die Ausgabe der Merkliste und eine PHP-Klasse zur Generierung des Star-Buttons über den Inserttag {{ivmImmoCollectionToggleCollection}}. 

##Hier sind die Templates, die angepasst wurden:
 
###Collection Template (Merkliste):
system\modules\ivm_immo_collection\templates\mod_immosearch_listcollection.html5
 
###Button Template zur Generierung des Inserttags:
system\modules\ivm_immo_collection\templates\toggleCollection.html5

###Detail Template:
templates\mod_immosearch_listresultdetail.html5

###Listentemplate
templates\mod_immosearch_listresult.html5



###Inserttag:
So wird der Inserttag verwendet:

!Achtung der 2. Parameter ist optional und wird nur auf der Merkliste gebraucht.:

{{ivmImmoCollectionToggleCollection::[WohnungsId]::[Optional: css selector des übergeordneten Elements, das bei unfeature aus dem DOM entfernt werden soll. (Siehe Collection Liste)]}}

####Beispiel im Listen- und Detailtemplate:
{{ivmImmoCollectionToggleCollection::51}}

####Oder mit optionalem 2. Parameter un der Merkliste:
{{ivmImmoCollectionToggleCollection::51::div.el_item}}




