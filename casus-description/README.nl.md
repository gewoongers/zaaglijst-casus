# Casus zaaglijst
Level: ~~easy~~/average/~~hard~~]

## Local installation

To spin up this application locally you can follow the following steps:
1. Fork the repository. See [here](https://docs.github.com/en/get-started/quickstart/fork-a-repo) to gather more 
information about forking a repository.
2. Open your terminal and run `composer install` to install all required packages. If you don't have composer installed
on your machine you can find more information 
[here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos).
3. Run `php artisan serve` and the application is running on localhost. You also can run the application with
   [laravel valet](https://laravel.com/docs/9.x/valet).
4. Go to `{{baseUrl}}/api/data` and you can see the current dataset. It is a json output, you can use  
[Postman](https://www.postman.com/downloads/) to read this dataset formatted. 

When the application is up you can begin your case!

## where can I begin?

There is a controller made named `ProductionStateController`. In this function you can find the `index` function. This
function fetches the data out `storage/data/ProductieStaat.json` and returns all production states.

This controller is your starting point. You can do whatever you think is needed to finish this case. Do you want to 
create an over engineered solution please do! Do you want to create tests? Please do! Again, you can do whatever you 
want to show your skills.

Oke, enough talking. Let's get the brain working.

## Casus

In hal 3, where all of our profiles are stored, profiles are picked daily to supply the production of a whole day.
A door consists of different profiles. A profile is indicated by the letter `G` as a prefix (so we have profiles G01 
to G72). There are 72 different profiles used to make our doors. Each profile is also available in all of our 12 colors.

Currently, the order picker looks at all of the doors that need to be made that day and based on that, estimates the 
profiles needed for the day. This is an inefficient process and takes an enormous amount of time to calculate what is 
needed for a day.

### What is the solution?

What would greatly help the order picker is a list of how many profiles in each color need to be picked that day
to meet the production demand. The profiles that are picked are almost all 3000mm long. To calculate how many
profiles are needed, we use an external API [optiCutter](https://www.opticutter.com/public/doc/api#introduction).
It calculates, based on the dimensions and the number of profiles that need to be cut of each size, how many
profiles are needed for each profile type. This API also includes the most efficient way to cut the profiles,
which means we can reduce waste with this solution!

Note that you do not actually need to use this API. What we are looking for is simply the input for this API.
The input we need to fill this API is the object below. This is the representation of how the object should be
formatted. The colors and profiles in this object are purely intended as an example.

Psst! If you want to use the API, the API has a sandbox where you can play around with it. 

```json
{
    "PROFIELKLEUR: RAL 9005 Gitzwart": {
        "G01": [
            {
                "length": 1987,
                "count": 2
            },
            {
                "length": 250,
                "count": 5
            },
            {
                "length": 557,
                "count": 2
            }
        ],
        "G21": [
            {
                "length": 876,
                "count": 5
            },
            {
                "length": 452,
                "count": 1
            }
        ]
    },
    "PROFIELKLEUR: Brons": {
        "G45": [
            {
              "length": 1222,
              "count": 5
            },
            {
              "length": 887,
              "count": 2
            }
        ],
        "G56": [
            {
                "length": 123,
                "count": 1
            }
        ]
    }
}
```

#### Structure of this object

The mapping of the data is described below. The `ProductieStaat.json` contains approximately 75 production states.
In each production state, you can find all of the data in the `saw` object.

```json
{
    "<saw.profielkleur.title>": {
        "<saw.*.title>": [
            {
                "length": "<saw.*.value>",
                "count": "<saw.*.amount>"
            },
            {...},
        ]
    },
    {...},
}
```

Voor deze casus hoef jij je alleen maar te focussen op het `saw` object. In dit object kan je alle profielen vinden die
gezaagd moeten worden die dag. De uitdaging is dat de profielen niet in de profielnamen ("G40") staan, maar als
bijvoorbeeld `liggerG40`. Van `liggerg40` moet je `G40` maken.

For this case, you only need to focus on the saw object. In this object, you can find all the profiles that need to be 
sawn that day. The challenge is that the profiles do not have the profile number ("G40") in their names, but rather as, 
for example, `liggerG40`. You need to change `liggerg40` to `G40`.

You can find the profile color in `profielkleur.title`. You can use this title as a unique identifier.

## Scope of this Case

The scope of this case is only to mutate the `ProductieStaat.json` file to the object that is needed for the input of
the optiCutter API. If you have been able to create a similar object, you meet the scope of this case.

## Requirements

To correctly mutate the dataset, you must meet a few requirements to arrive at the correct answer:
- You only need to take the profiles that contain a G number (you can find the profiles in the `saw` object), for example:
    - `OnderbovenProfielg41` is a profile.
    - `exactinputtaatsdeur_z` is not a profile because it does not contain a G number.
- The profile names, such as `OnderbovenProfielg41`, must be changed to a G number. In this case, it should be `G41`.
  With this, you can later map all profiles with the correct color together.
- There are also two profiles that fit together and thus have the same color and size. For these types of profile
  combinations, you have, for example, `staanderg54g56taatsdeur`. There are two G numbers in this profile. So if you have
  a `value` in this object of `2396` and an `amount` of `2`, then you get the following result for a profile color of
  `PROFIELKLEUR: RAL 7032 Kiezel grijs`:
```json
{
    "PROFIELKLEUR: RAL 7032 Kiezel grijs": {
        "G54": {
            "length": 2396,
            "count": 2
        },
        "G56": {
            "length": 2396,
            "count": 2
        },
        {...},
    }
}
```
- The G numbers and colors combination need to be mapped together. I will give an example here of the current situation 
and the desired situation that is needed for the input of the optiCutter API. Note that this is a subset of the 
production states.

#### Huidige situatie
```json
{
    "id": 123,
    "saw": {
        "liggerg40": {
            "title": "Ligger G40",
            "amount": 2,
            "value": 1600
        },
        "staanderg54g56taatsdeur": {
            "title": "Staander G54 + G56 taatsdeur",
            "amount": 2,
            "value": 2396
        },
        "profielkleur": {
            "title": "PROFIELKLEUR: RAL 7032 Kiezel grijs"
        }
    }
},
{
    "id": 456,
    "saw": {
        "staandersg70verstekdeur": {
            "title": "Staanders G70 verstek deur",
            "amount": 2,
            "value": 2400
        },
        "staanderg62g69deur": {
            "title": "Staander G69 deur",
            "amount": 1,
            "value": 2400
        },
        "profielkleur": {
            "title": "PROFIELKLEUR: Leem"
        }
    }
},
{
    "id": 789,
    "saw": {
        "staandersg70verstekdeur": {
            "title": "Staanders G70 verstek deur",
            "amount": 2,
            "value": 2785
        },
        "staanderg62g69deur": {
            "title": "Staander G69 deur",
            "amount": 1,
            "value": 2785
        },
        "liggerg40": {
            "title": "Ligger G40",
            "amount": 2,
            "value": 1600
        },
        "profielkleur": {
            "title": "PROFIELKLEUR: Leem"
        }
    }
},
{
    "id": 012,
    "saw": {
        "liggerg40": {
            "title": "Ligger G40",
            "amount": 2,
            "value": 1600
        },
        "profielkleur": {
          "title": "PROFIELKLEUR: Leem"
        }
    }
},
{...},
```

#### Gewenste situatie

```json
{
    "PROFIELKLEUR: RAL 7032 Kiezel grijs": {
        "G40": [
            {
                "length": 1600,
                "count": 2
            }
        ],
        "G54": [
            {
                "length": 2396,
                "count": 2
            }
        ],
        "G56": [
            {
                "length": 2396,
                "count": 2
            }
        ]
    },
    "PROFIELKLEUR: Leem": {
        "G70": [
            {
              "length": 2785,
              "count": 2
            }
        ],
        "G62": [
            {
                "length": 2785,
                "count": 1
            }
        ],
        "G69": [
            {
                "length": 2785,
                "count": 1
            }
        ],
        "G40": [
            {
                "length": 1600,
                "count": 1
            },
            {
                "length": 2,
                "count": 1600
            }
        ]
    }
}
```

### Verdere informatie
- When you have a question please go to the
  [Q&A bord](https://github.com/gewoongers/zaaglijst-casus/discussions/categories/q-a).
- When you have an idea for this case, please let me know at the
  [ideeën bord](https://github.com/gewoongers/zaaglijst-casus/discussions/categories/ideas)
- Did you find a bug? Please add an issue
  [here](https://github.com/gewoongers/zaaglijst-casus/issues/new).
