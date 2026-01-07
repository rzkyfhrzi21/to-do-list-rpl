<?php
require_once 'functions/function_statistik.php';
?>

<div class="page-heading">
    <h3>Statistik To-Do List</h3>
    <p class="text-subtitle text-muted">
        Ringkasan statistik tugas berdasarkan aktivitas pengguna.
    </p>
</div>

<div class="page-content">
    <section class="row">

        <!-- =========================
             TOTAL TUGAS
        ========================= -->
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon purple mb-2">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Jumlah Tugas</h6>
                    <h6 class="font-extrabold mb-0"><?= $totalTask; ?></h6>
                </div>
            </div>
        </div>

        <!-- =========================
             TUGAS SELESAI
        ========================= -->
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon green mb-2">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Tugas Selesai</h6>
                    <h6 class="font-extrabold mb-0"><?= $totalSelesai; ?></h6>
                </div>
            </div>
        </div>

        <!-- =========================
             TUGAS BELUM SELESAI
        ========================= -->
        <div class="col-6 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="stats-icon red mb-2">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <h6 class="text-muted font-semibold">Belum Selesai</h6>
                    <h6 class="font-extrabold mb-0"><?= $totalBelum; ?></h6>
                </div>
            </div>
        </div>

    </section>
</div>