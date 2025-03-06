class ToastService {
    getDefaultFomanticUiSettings() {
        return [
            {
                preserveHTML: false,
            },
            {},
        ];
    }
}

export default Object.freeze(new ToastService());
