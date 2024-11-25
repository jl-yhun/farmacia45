import { mount, shallowMount } from "@vue/test-utils";
import ModalProveedorSelector from "../components/ordenes-compra/ModalProveedorSelector.vue";

describe('arrangeListOfDealers', () => {

    it('sort correctly', () => {
        const component = shallowMount(ModalProveedorSelector, {
            global: {
                stubs: {
                    'Button': {
                        template: '<Button/>'
                    },
                    'NumberInput': {
                        template: '<NumberInput/>'
                    },
                    'NumberInputGroup': {
                        template: '<NumberInputGroup/>'
                    },
                    'Select': {
                        template: '<Select/>'
                    },
                    'Notification': {
                        template: '<Notification/>'
                    }
                }
            },
            props: {
                productoId: 1
            }
        })

        const testCases = [
            {
                input: [
                    {
                        "id": 1,
                        "nombre": "Quepharma",
                        "pivot": {
                            "disponible": 1,
                            "default": 1,
                            "precio": 8
                        }
                    },
                    {
                        "id": 2,
                        "nombre": "Levic",
                        "pivot": {
                            "disponible": 1,
                            "default": 0,
                            "precio": 7
                        }
                    },
                    {
                        "id": 3,
                        "nombre": "Depot",
                        "pivot": {
                            "disponible": 1,
                            "default": 1,
                            "precio": 5
                        }
                    }
                ],
                expected: [
                    {
                        "id": 3,
                        "nombre": "Depot",
                        "pivot": {
                            "disponible": 1,
                            "default": 1,
                            "precio": 5
                        }
                    },
                    {
                        "id": 1,
                        "nombre": "Quepharma",
                        "pivot": {
                            "disponible": 1,
                            "default": 1,
                            "precio": 8
                        }
                    },
                    {
                        "id": 2,
                        "nombre": "Levic",
                        "pivot": {
                            "disponible": 1,
                            "default": 0,
                            "precio": 7
                        }
                    },

                ]
            },
            {
                input: [
                    {
                        nombre: 'Levic',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 13.02
                        }
                    },
                    {
                        nombre: 'Quepharma',
                        pivot: {
                            disponible: 1,
                            default: 1,
                            precio: 13.42
                        }
                    },
                    {
                        nombre: 'Nadro',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 15.53
                        }
                    },
                    {
                        nombre: 'Equilibrio',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 12.40
                        }
                    }
                ],
                expected: [
                    {
                        nombre: 'Quepharma',
                        pivot: {
                            disponible: 1,
                            default: 1,
                            precio: 13.42
                        }
                    },
                    {
                        nombre: 'Equilibrio',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 12.40
                        }
                    },
                    {
                        nombre: 'Levic',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 13.02
                        }
                    },
                    {
                        nombre: 'Nadro',
                        pivot: {
                            disponible: 1,
                            default: 0,
                            precio: 15.53
                        }
                    }
                ]
            },
            {
                input: [
                    {
                        nombre: 'Quepharma',
                        pivot: {
                            disponible: 0,
                            default: 1,
                            precio: 10
                        }
                    },
                ],
                expected: [
                ]
            }
        ]

        testCases.forEach((testCase) => {
            var actual = component.vm.arrangeListOfDealers(testCase.input)
            expect(actual).toStrictEqual(testCase.expected)
        })

    });

});
