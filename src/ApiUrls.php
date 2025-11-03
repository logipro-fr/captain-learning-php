<?php

namespace CaptainLearningPhp;

/**
 * Gestionnaire des URLs de l'API Captain Learning
 *
 * Cette classe centralise la gestion des URLs de l'API Captain Learning,
 * permettant de basculer facilement entre les environnements (prod, dev, test).
 *
 * @package CaptainLearningPhp
 * @author Frédéric Royet <frederic.royet@logipro.com>
 * @since 1.0.0
 *
 * @example
 * Utilisation avec l'URL de production (par défaut)
 * ```php
 * $apiUrls = new ApiUrls();
 * echo $apiUrls->createFormation(); // https://app.captain-learning.com/api/external/v1/formation
 *
 * $apiUrls = new ApiUrls('http://localhost:8080');
 * echo $apiUrls->createFormation(); // http://localhost:8080/api/external/v1/formation
 * ```
 */
class ApiUrls
{
    public const BASE_URL_PROD = 'https://app.captain-learning.com';

    public const PREFIX_API = '/api/external';

    public const CREATE_FORMATION = self::PREFIX_API . '/v1/formation';

    private string $baseUrl = self::BASE_URL_PROD;

    public function __construct(?string $baseUrl = null)
    {
        if ($baseUrl !== null) {
            $this->baseUrl = $baseUrl;
        }
    }
    /**
     * Récupère l'URL de base configurée
     *
     * @return string L'URL de base (avec ou sans slash final)
     *
    */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    /**
     * Récupère l'URL complète pour la création d'une formation
     */
    public function createFormation(): string
    {
        return $this->getBaseUrl() . self::CREATE_FORMATION;
    }
}
