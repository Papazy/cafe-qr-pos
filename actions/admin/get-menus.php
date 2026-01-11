<?php

    function getMenus($conn, $kategori) {
        $query = "
            SELECT 
                menu.id,
                menu.nama,
                menu.harga,
                menu.gambar,
                menu.created_at,
                kategori.nama As kategori_nama
            FROM menu
            JOIN kategori ON menu.kategori_id = kategori.id
        ";

        if($kategori !== 'semua') {
            $query .= " WHERE kategori.nama = :kategori";
        }


        // order by
        $query .= " ORDER BY kategori.nama, menu.nama";

        $stmt = $conn->prepare($query);
        if($kategori !== 'semua') {
            $stmt->execute(['kategori' => $kategori]);
        } else {
            $stmt->execute();
        }

        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $menus;
    }

    function getKategoriList($conn) {
        $stmt = $conn->query("SELECT * FROM kategori ORDER BY nama");
        return $stmt->fetchAll();
    }