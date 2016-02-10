<?php namespace Stormpath\Tests\Cache;

use Stormpath\Cache\Tags\CacheTagExtractor;

class CacheTagExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractSingleExpansionTags()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@fervo.se",
  "email": "magnus+stormpathtest@fervo.se",
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

        $this->assertEquals(["https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"], $tags);
    }


    public function testExtractCollectionExpansion()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@fervo.se",
  "email": "magnus+stormpathtest@fervo.se",
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

        $this->assertEquals(0, count(array_diff([
            "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups",
            "https://api.stormpath.com/v1/groups/4EOPdPzOz8SrIaPmXxOAJY",
            "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8"
        ], $tags)));
    }

    public function testExtractLimitedCollectionExpansion()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@fervo.se",
  "email": "magnus+stormpathtest@fervo.se",
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

        $tags = CacheTagExtractor::extractCacheTags($document, "groups(limit:10,offset:1)");

        $this->assertEquals(0, count(array_diff([
            "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM/groups",
            "https://api.stormpath.com/v1/groups/s8uvSRUj16PWmScc2vHO8"
        ], $tags)));
    }

    public function testExtractMultipleExpansionTags()
    {
        $docJson = <<<EOJ
{
  "href": "https://api.stormpath.com/v1/accounts/3VuAsUL9zeBGZRtB48fVZM",
  "username": "magnus+stormpathtest@fervo.se",
  "email": "magnus+stormpathtest@fervo.se",
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
    "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE",
    "name": "Stormpath Administrators",
    "description": "Default directory for accounts and groups that may access Stormpath IAM.",
    "status": "ENABLED",
    "createdAt": "2016-02-09T15:54:50.167Z",
    "modifiedAt": "2016-02-09T15:54:50.167Z",
    "tenant": {
      "href": "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk"
    },
    "provider": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/provider"
    },
    "customData": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/customData"
    },
    "passwordPolicy": {
      "href": "https://api.stormpath.com/v1/passwordPolicies/3VtCLkEb2taKu7zHsdPpVE"
    },
    "accountCreationPolicy": {
      "href": "https://api.stormpath.com/v1/accountCreationPolicies/3VtCLkEb2taKu7zHsdPpVE"
    },
    "accounts": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/accounts"
    },
    "applicationMappings": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/applicationMappings"
    },
    "applications": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/applications"
    },
    "groups": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/groups"
    },
    "organizations": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/organizations"
    },
    "organizationMappings": {
      "href": "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE/organizationMappings"
    }
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

        $tags = CacheTagExtractor::extractCacheTags($document, "tenant,directory");

        $this->assertEquals(0, count(array_diff([
            "https://api.stormpath.com/v1/tenants/3VsmPQTUvQl52zSTeqapBk",
            "https://api.stormpath.com/v1/directories/3VtCLkEb2taKu7zHsdPpVE",
        ], $tags)));
    }
}
