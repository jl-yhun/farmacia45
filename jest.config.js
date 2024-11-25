module.exports = {
    testEnvironment: "jsdom",
    testRegex: 'resources/assets/js/.*.test.js$',
    moduleFileExtensions: [
        'js', 'json', 'vue', 'ts'
    ],
    testEnvironmentOptions: {
        customExportConditions: ["node", "node-addons"],
    },
    'transform': {
        '^.+\\.js$': 'babel-jest',
        '.*\\.(vue)$': '@vue/vue3-jest',
        "^.+\\.tsx?$": "ts-jest"
    },
}