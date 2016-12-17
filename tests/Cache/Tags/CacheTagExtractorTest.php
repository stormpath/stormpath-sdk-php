<?php namespace Stormpath\Tests\Cache;

use Stormpath\Cache\Tags\CacheTagExtractor;

class CacheTagExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractSingleExpansionTags()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@testmail.stormpath.com",
  "email": "magnus+stormpathtest@testmail.stormpath.com",
  "givenName": "Magnus",
  "middleName": null,
  "surname": "Nordlander",
  "fullName": "Magnus Nordlander",
  "status": "ENABLED",
  "createdAt": "2016-02-09T15:54:50.178Z",
  "modifiedAt": "2016-02-09T15:54:50.178Z",
  "emailVerificationToken": null,
  "customData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData"
  },
  "providerData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData"
  },
  "directory": {
    "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
  },
  "tenant": {
    "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk",
    "name": "tough-crown",
    "key": "tough-crown",
    "createdAt": "2016-02-09T15:54:50.158Z",
    "modifiedAt": "2016-02-09T15:54:50.608Z",
    "customData": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/customData"
    },
    "organizations": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/organizations"
    },
    "applications": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/applications"
    },
    "directories": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/directories"
    },
    "accounts": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/accounts"
    },
    "agents": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/agents"
    },
    "groups": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/groups"
    },
    "idSites": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/idSites"
    }
  },
  "groups": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups"
  },
  "applications": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications"
  },
  "groupMemberships": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships"
  },
  "apiKeys": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys"
  },
  "accessTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens"
  },
  "refreshTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens"
  }
}
EOJ;
        $document = json_decode($docJson);

        $tags = CacheTagExtractor::extractCacheTags($document, "tenant");

       $this->assertEquals(count($tags), count(array_intersect([
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/idSites',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/groups',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/agents',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/accounts',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/directories',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/applications',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/organizations',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk/customData',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM',
        ], $tags)));
    }


    public function testExtractCollectionExpansion()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@testmail.stormpath.com",
  "email": "magnus+stormpathtest@testmail.stormpath.com",
  "givenName": "Magnus",
  "middleName": null,
  "surname": "Nordlander",
  "fullName": "Magnus Nordlander",
  "status": "ENABLED",
  "createdAt": "2016-02-09T15:54:50.178Z",
  "modifiedAt": "2016-02-09T15:54:50.178Z",
  "emailVerificationToken": null,
  "customData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData"
  },
  "providerData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData"
  },
  "directory": {
    "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
  },
  "tenant": {
    "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
  },
  "groups": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups",
    "offset": 0,
    "limit": 25,
    "size": 2,
    "items": [
      {
        "href": "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY",
        "name": "Bazbar",
        "description": "Quux",
        "status": "ENABLED",
        "createdAt": "2016-02-10T15:54:18.575Z",
        "modifiedAt": "2016-02-10T15:54:18.575Z",
        "customData": {
          "href": "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/customData"
        },
        "directory": {
          "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
        },
        "tenant": {
          "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
        },
        "accounts": {
          "href": "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/accounts"
        },
        "accountMemberships": {
          "href": "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/accountMemberships"
        },
        "applications": {
          "href": "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/applications"
        }
      },
      {
        "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8",
        "name": "Foobar",
        "description": "1234 Test",
        "status": "ENABLED",
        "createdAt": "2016-02-10T15:36:54.425Z",
        "modifiedAt": "2016-02-10T15:36:54.425Z",
        "customData": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/customData"
        },
        "directory": {
          "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
        },
        "tenant": {
          "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
        },
        "accounts": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accounts"
        },
        "accountMemberships": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accountMemberships"
        },
        "applications": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/applications"
        }
      }
    ]
  },
  "applications": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications"
  },
  "groupMemberships": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships"
  },
  "apiKeys": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys"
  },
  "accessTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens"
  },
  "refreshTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens"
  }
}
EOJ;
        $document = json_decode($docJson);

        $tags = CacheTagExtractor::extractCacheTags($document, "groups");

        $this->assertEquals(count($tags), count(array_intersect([
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/applications',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accountMemberships',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accounts',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/customData',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8',
            'https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/applications',
            'https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/accountMemberships',
            'https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/accounts',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY/customData',
            'https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM',
        ], $tags)));
    }

    public function testExtractLimitedCollectionExpansion()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@testmail.stormpath.com",
  "email": "magnus+stormpathtest@testmail.stormpath.com",
  "givenName": "Magnus",
  "middleName": null,
  "surname": "Nordlander",
  "fullName": "Magnus Nordlander",
  "status": "ENABLED",
  "createdAt": "2016-02-09T15:54:50.178Z",
  "modifiedAt": "2016-02-09T15:54:50.178Z",
  "emailVerificationToken": null,
  "customData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData"
  },
  "providerData": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData"
  },
  "directory": {
    "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
  },
  "tenant": {
    "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
  },
  "groups": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups",
    "offset": 1,
    "limit": 10,
    "size": 2,
    "items": [
      {
        "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8",
        "name": "Foobar",
        "description": "1234 Test",
        "status": "ENABLED",
        "createdAt": "2016-02-10T15:36:54.425Z",
        "modifiedAt": "2016-02-10T15:36:54.425Z",
        "customData": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/customData"
        },
        "directory": {
          "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE"
        },
        "tenant": {
          "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
        },
        "accounts": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accounts"
        },
        "accountMemberships": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accountMemberships"
        },
        "applications": {
          "href": "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/applications"
        }
      }
    ]
  },
  "applications": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications"
  },
  "groupMemberships": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships"
  },
  "apiKeys": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys"
  },
  "accessTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens"
  },
  "refreshTokens": {
    "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens"
  }
}
EOJ;
        $document = json_decode($docJson);

        $tags = CacheTagExtractor::extractCacheTags($document);

        $this->assertEquals(count($tags), count(array_intersect([
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/refreshTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/accessTokens',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/apiKeys',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groupMemberships',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/applications',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/applications',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accountMemberships',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/accounts',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8/customData',
            'https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups',
            'https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk',
            'https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/providerData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/customData',
            'https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM',
        ], $tags)));
    }
}
