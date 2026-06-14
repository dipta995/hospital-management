<script>
    window.InvoiceReferCommission = {
        selectedRefer: null,

        setRefer(refer) {
            if (!refer || !(refer.referID || refer.id)) {
                this.selectedRefer = null;
                return;
            }

            this.selectedRefer = {
                referID: refer.referID || refer.id,
                percent: parseFloat(refer.percent) || 0,
                has_custom_percent: !!refer.has_custom_percent,
                custom_percents: refer.custom_percents || {},
            };
        },

        lineAmount(product) {
            if (!this.selectedRefer) {
                return 0;
            }

            const price = parseFloat(product.price) || 0;

            if (this.selectedRefer.has_custom_percent) {
                const percentage = this.selectedRefer.custom_percents[product.category_id] !== undefined
                    ? parseFloat(this.selectedRefer.custom_percents[product.category_id])
                    : this.selectedRefer.percent;

                return Math.round((percentage * price / 100) * 100) / 100;
            }

            return Math.round((this.selectedRefer.percent * price / 100) * 100) / 100;
        },

        calculate(products, discountAmount, grossTotal) {
            if (!this.selectedRefer) {
                return 0;
            }

            const refer = this.selectedRefer;

            if (refer.has_custom_percent) {
                let amount = 0;

                products.forEach((product) => {
                    const price = parseFloat(product.price) || 0;
                    const categoryId = product.category_id;
                    const percentage = refer.custom_percents[categoryId] !== undefined
                        ? parseFloat(refer.custom_percents[categoryId])
                        : refer.percent;
                    amount += (percentage * price) / 100;
                });

                return Math.max(0, Math.round((amount - discountAmount) * 100) / 100);
            }

            const total = grossTotal !== undefined
                ? grossTotal
                : products.reduce((sum, product) => sum + (parseFloat(product.price) || 0), 0);

            return Math.max(0, Math.round((((refer.percent * total) / 100) - discountAmount) * 100) / 100);
        },
    };
</script>
