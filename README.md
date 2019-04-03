Detail Template:
templates\mod_immosearch_listresultdetail.html5

Listentemplate
templates\mod_immosearch_listresult.html5

Collection Template:
system\modules\ivm_immo_collection\templates\mod_immosearch_listcollection.html5

Button Template:
system\modules\ivm_immo_collection\templates\toggleCollection.html5

Inserttag:
{{ivmImmoCollectionToggleCollection::**WohnungsId**::**Optional: css selector des übergeordneten Elements, das bei unfeature aus dem DOM entfernt werden soll. (Siehe Collection Liste)**}}

Beispiel:
{{ivmImmoCollectionToggleCollection::51}}
Oder mit optionalem 2. Parameter:
{{ivmImmoCollectionToggleCollection::51::div.el_item}}


