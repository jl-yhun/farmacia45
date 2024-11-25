export function useQueryBuilder() {

    const buildQueryParams = (queryParamsString, newParams) => {
        let currentParams = new URLSearchParams(queryParamsString);

        for (const keyParam in newParams) {
            if (Object.hasOwnProperty.call(newParams, keyParam)) {
                const valueParam = newParams[keyParam];
                if (valueParam == '' || valueParam == null) {
                    currentParams.delete(keyParam);
                    continue
                }

                if (valueParam.constructor === Array) {
                    var added = false;
                    for (const value of valueParam) {
                        if (!added) {
                            currentParams.set(keyParam + "[]", value)
                            added = true
                        }
                        else
                            currentParams.append(keyParam + "[]", value)
                    }
                } else {
                    currentParams.set(keyParam, valueParam)
                }
            }
        }
        return currentParams.toString()
    }

    const buildUrlWithNewParams = (newParams) => {
        return `${location.origin}${location.pathname}?${newParams}`
    }

    return { buildQueryParams, buildUrlWithNewParams };
}