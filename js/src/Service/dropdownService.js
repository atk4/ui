class DropdownService {
    getDefaultFomanticUiSettings() {
        return [
            {
                preserveHTML: false,
            },
            {},
        ];
    }
}

export default Object.freeze(new DropdownService());
