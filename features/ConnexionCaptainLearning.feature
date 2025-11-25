# language: fr
Fonctionnalité: créer une connexion à l'api Captain Learning
    ETQ OF je peux avec le client captain learning php me connecter à l'api captain learning

Scénario: Connexion à l'API de Captain Learning
    Étant donné que je dispose d'une clé API "sk_7a5f1b27ad33f4b6455ce8f96ca90bf2"
    Et que je dispose d'un apiKeyId "cl_apk_690b25988d814d2b"
    Et que l'URL de l'API est "http://monsite.fr"
    Et que le tenantId est "mon_tenantId"
    Quand je me connecte à l'API Captain Learning
    Alors je reçois un token de connexion valide
    Et la connexion est établie avec succes