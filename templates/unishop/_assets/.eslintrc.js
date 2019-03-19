module.exports = {
    "env": {
        "browser": true
    },
    "extends": "eslint:recommended",
    "rules": {
        "indent": [
            "error",
            2
        ],
        "linebreak-style": [
            "off",
            "unix"
        ],
        "quotes": [
            "warn",
            "single"
        ],
        "semi": [
            "error",
            "always"
        ],
        "no-undef": [
            "error"
        ],
        "no-extra-semi": [
            "off"
        ]
    },
    "globals": {
        "$": false,
        "jQuery": false
    }
};