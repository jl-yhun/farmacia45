import { useQueryBuilder } from "../composables/query-builder";

describe('useQueryBuilder', () => {
    const { buildQueryParams, buildUrlWithNewParams } = useQueryBuilder()
    it('build correctly when exist', () => {

        var actual = buildQueryParams('term=amox', { term: 'amoxci' })
        expect(actual).toBe('term=amoxci')
    });

    it('build correctly when does not exist', () => {
        var actual = buildQueryParams('term=amox', { term: 'amox', 'limit': [0, 20] })
        expect(actual).toBe(encodeURI('term=amox&limit[]=0&limit[]=20'))
    });

    it('build correctly when exist array', () => {
        var actual = buildQueryParams('term=amox&limit[]=10&limit[]=20', { term: 'amox', 'limit': [0, 30] })
        expect(actual).toBe(encodeURI('term=amox&limit[]=0&limit[]=30'))
    });

    it('build correctly when empty', () => {
        var actual = buildQueryParams('', { caducidad: '2023-10-10' })
        expect(actual).toBe(encodeURI('caducidad=2023-10-10'))
    });

    it('build correctly when no valid param provided', () => {
        var actual = buildQueryParams('term=amox&caducidad=2023-10-10', { term: '', 'caducidad': '2023-10-10' })
        expect(actual).toBe(encodeURI('caducidad=2023-10-10'))
    });

    it('build url correclt', () => {
        var actual = buildUrlWithNewParams('caducidad=2023-10-10')
        expect(actual).toBe(encodeURI('http://localhost/?caducidad=2023-10-10'))
    })

});
