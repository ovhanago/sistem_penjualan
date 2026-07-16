                background-color: transparent !important;
                border-color: #000000 !important;
                box-shadow: none !important;
            }

            /* Draw clean borders on table rows for printed report layout */
            .table {
                border: none !important;
            }
            .table th, .table td {
                border-bottom: 1px solid #000000 !important;
                color: #000000 !important;
            }

            /* Print-friendly status badges (transparent background with a black border) */
            .badge-status {
                border: 1px solid #000000 !important;
                background-color: transparent !important;
                color: #000000 !important;
                padding: 4px 8px !important;
                border-radius: 4px !important;
            }
        }
    </style>
</head>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card p-3 bg-primary text-white">
                            <span class="text-white-50 small d-block">Total Pendapatan (Status Success/Selesai)</span>
                        <div class="stat-card p-3 bg-light">
                            <span class="text-muted small d-block">Total Pendapatan (Status Success/Selesai)</span>
                            <h4 class="fw-bold mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h4>
                        </div>
                    </div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>