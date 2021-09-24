const globalUtils = {
    uraQueryParamToJson(rawUrl) {

        let url = new URL(rawUrl);
        let searchParams = url.searchParams;
        let result = {};

        // Display the key/value pairs
        for (var entry of searchParams.entries()) {
            result[entry[0]] = entry[1];
        }
        return result;
    },
    arrayToCsv(arr) {
        return arr.join(',');
    },
    decodeFromCsv(str) {
        if (str) {
            return str.split(',');
        }
        return null;
    }
}

