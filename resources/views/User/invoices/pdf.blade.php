<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details, .items { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        @media (max-width: 600px) {
            table, th, td { font-size: 14px; padding: 6px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture #{{ e($invoice->invoice_number) }}</h1>
    </div>

    <div class="details">
        <p><strong>Émise le :</strong> {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : 'Non spécifié' }}</p>
        <p><strong>Sous-total :</strong> ${{ number_format($invoice->subtotal ?? 0, 2) }}</p>
        <p><strong>Réduction :</strong> ${{ number_format($invoice->discount ?? 0, 2) }}</p>
        <p><strong>Total :</strong> ${{ number_format($invoice->total ?? 0, 2) }}</p>
        <p><strong>Méthode de paiement :</strong> {{ e($invoice->payment_method ?? 'Non spécifié') }}</p>
        <p><strong>ID de transaction :</strong> {{ e($invoice->payment_id ?? 'Non spécifié') }}</p>
    </div>

    <div class="items">
        <h3>Articles</h3>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Prix</th>
                    <th>Réduction</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if (is_array($invoice->items) && !empty($invoice->items))
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ e($item['course_title'] ?? 'Cours inconnu') }}</td>
                            <td>${{ number_format($item['price'] ?? 0, 2) }}</td>
                            <td>${{ number_format($item['discount'] ?? 0, 2) }}</td>
                            <td>${{ number_format(($item['price'] ?? 0) - ($item['discount'] ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Aucun article disponible</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>