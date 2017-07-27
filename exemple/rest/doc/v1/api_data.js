define({ "api": [
  {
    "type": "delete",
    "url": "cornichons/:id",
    "title": "",
    "description": "<p>Supprime un cornichon</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "DeleteCornichonsId"
  },
  {
    "type": "get",
    "url": "cornichons/",
    "title": "",
    "description": "<p>Retourne la liste des cornichons</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "GetCornichons"
  },
  {
    "type": "get",
    "url": "cornichons/:id",
    "title": "",
    "description": "<p>Retourne un cornichon</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "fields",
            "description": "<p>Champs à récupérer</p>"
          }
        ]
      }
    },
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "GetCornichonsId"
  },
  {
    "type": "get",
    "url": "cornichons/:id/vendeurs/",
    "title": "",
    "description": "<p>Retourne la liste des vendeurs de cornichons</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "GetCornichonsIdVendeurs"
  },
  {
    "type": "get",
    "url": "cornichons/:id/vendeurs/:id",
    "title": "",
    "description": "<p>Retourne un vendeur de cornichon</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "GetCornichonsIdVendeursId"
  },
  {
    "type": "patch",
    "url": "cornichons/:id",
    "title": "",
    "description": "<p>Modifie un cornichon (partiel)</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "nom",
            "description": "<p>Nom du cornichon</p>"
          }
        ]
      }
    },
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "PatchCornichonsId"
  },
  {
    "type": "post",
    "url": "cornichons/",
    "title": "",
    "description": "<p>Créé un cornichon</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "admin"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "nom",
            "description": "<p>Nom du cornichon</p>"
          }
        ]
      }
    },
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "PostCornichons"
  },
  {
    "type": "put",
    "url": "cornichons/:id",
    "title": "",
    "description": "<p>Modifie un cornichon</p>",
    "group": "cornichons",
    "version": "1.0.0",
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "nom",
            "description": "<p>Nom du cornichon</p>"
          }
        ]
      }
    },
    "filename": "v1/cornichons.php",
    "groupTitle": "cornichons",
    "name": "PutCornichonsId"
  }
] });
