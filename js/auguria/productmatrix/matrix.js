Matrix = Class.create();
Matrix.prototype = {
		
    initialize: function(config){
    	this.basePrice = parseFloat(config.basePrice); // base price for configurable product
    	
    	this.tierPrice = config.tierPrice;
    	this.taxConfig = config.taxConfig;
    	this.productPrices = [];
    	
    	this.columnTotalQtyId = 'column-total-qty';
    	this.columnTotalPriceId = 'column-total-price';
		this.columnTotalQtyValue = 0;
		this.columnTotalPriceValue = 0;
    },

    // format price with currency (locale configuration)
    formatPrice: function(price){
    	
        if (this.taxConfig.includeTax) {
            var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            var excl = price - tax;
            var incl = excl*(1+(this.taxConfig.currentTax/100));
        } else {
            var tax = price * (this.taxConfig.currentTax / 100);
            var excl = price;
            var incl = excl + tax;
        }

        if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
            price = incl;
        } else {
            price = excl;
        }

        var str = '';
        
        if (optionsPrice.showBothPrices) {
            str+= formatCurrency(excl, optionsPrice.priceFormat) + ' (' + formatCurrency(price, optionsPrice.priceFormat) + ' ' + this.taxConfig.inclTaxTitle + ')';
        } else {
            str += formatCurrency(price, optionsPrice.priceFormat)
        }
        
        return str;
    },
    
    // calcul final price
    loadPrice: function(productId, options, basePrice, format){
    	var finalPrice = basePrice ? basePrice : this.basePrice;
    	var basePrice = basePrice ? basePrice : this.basePrice;

    	options.each(function(price){
    		if(price.pricing_value){
        	    if(parseInt(price.is_percent) > 0){ // percent
        	    	finalPrice = finalPrice + basePrice * (parseFloat(price.pricing_value)/100);
        	    }else{ // fixe
        	    	finalPrice = finalPrice + parseFloat(price.pricing_value);
        	    }
    		}
    	});

    	// store format (currency...)
    	finalPrice = format ? this.formatPrice(finalPrice) : finalPrice;

    	return finalPrice;
    },
    
    // add product price in matrix
    addProductPrice: function(productId, price, options){
    	this.productPrices[productId] = {'unit_price': price, 'old_unit_price': price, 'options': options};
    },
    
    // reload matrix
    reload: function(productId, qty, oldQty){
    	qty = parseInt(qty);
    	
    	if(this.tierPrice.length){
    		this.reloadUnitPrice(productId, qty, oldQty) // reload unit price (tier price) 
    	}
    	
    	this.reloadFinalPrice(productId, qty, oldQty); // reload final price
    	this.reloadQty(productId, qty, oldQty); // reload total
    	this.reloadPrice(productId, qty, oldQty); // reload quantity
    },
    
    // reload unit price coz of tier price
    reloadUnitPrice: function(productId, qty, oldQty){
    	var basePrice = this.basePrice; // group price calculated
    	var unitPrice = this.productPrices[productId].unit_price; // required when adding a new product price
    	var qtyToCheck = 0;
    	
    	this.productPrices[productId].old_unit_price = unitPrice;
    	
    	// get tier price to use
    	var increment = 1;
    	this.tierPrice.each(function(tierPrice){
    		var tierPriceQty = parseInt(tierPrice.price_qty);
    		var tierProductPrice = parseFloat(tierPrice.price);
    		
    		if(increment == 1 && qty < tierPriceQty){
    			unitPrice = basePrice; // no tier price, get the base price
    		}else if(qtyToCheck <= tierPriceQty && qty >= tierPriceQty){
    			unitPrice = (tierProductPrice < basePrice) ? tierProductPrice : basePrice; // new unitPrice
    			qtyToCheck = tierPriceQty; // qty for available tier price
    		}
    		
    		increment ++;
    	});
    	
    	unitPrice = this.loadPrice(productId, this.productPrices[productId].options, unitPrice); // get new unit price with options variation
    	
    	this.productPrices[productId].unit_price = unitPrice; // update product price information in matrix
    	$('unit-price-' + productId).update(this.formatPrice(unitPrice));
    },
    
    // reload final price
    reloadFinalPrice: function(productId, qty, oldQty){
        var value = this.productPrices[productId].unit_price;
        $('total-'+productId).update(this.formatPrice(value*qty));
    },
    
    // reload quantity
    reloadQty: function(productId, qty, oldQty){
    	var qtyTotal = this.columnTotalQtyValue;
    	var newQtyTotal = qtyTotal-oldQty+qty;
    	
    	this.columnTotalQtyValue = newQtyTotal;
    	$(this.columnTotalQtyId).update(newQtyTotal);
    },
    
    // reload grand total
    reloadPrice: function(productId, qty, oldQty){
    	var unitPrice = this.productPrices[productId].unit_price;
    	var oldUnitPrice = this.productPrices[productId].old_unit_price;
    	var priceTotal = this.columnTotalPriceValue;
    	
    	// substract old unit price with old quantity to the total and add the new unit price mutliplied by the new quantity
    	var newPriceTotal = priceTotal - oldUnitPrice*oldQty + unitPrice*qty;
    	
    	this.columnTotalPriceValue = newPriceTotal;
    	$(this.columnTotalPriceId).update(this.formatPrice(newPriceTotal));
    },
}