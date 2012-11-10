# Author: Daniel Mauno Pettersson at Agigen http://agigen.se/

require './billogram_client.rb'

# Setup your credentials
api_id = "4e8eddb37cff852384e8eddb37d019"
password = 'myPassword'

# Connect to the API
billogram = BillogramClient.new( api_id: api_id, password: password)

# Create a new invoice
new_invoice = billogram.createInvoice(
    invoice_date: '2011-11-18',
    due_days: 30,
    customer_no: 1,
    delivery_type: 'Letter',
    items: {
         0 => {title: 'Cake', num: 2, price: 139}
    }
)

# Show some debug information about the newly created invoice
puts new_invoice

# Load back the same invoice from Billogram
same_invoice = billogram.getInvoice(new_invoice['invoice_no'])
# ...and show some debug information
puts same_invoice
