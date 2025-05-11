@extends('layouts.admin')

@section('title', 'Admin Panel')

@section('styles')
<style>
.container {
    width: 80%;
    max-width: 600px;
    margin: 60px auto;
    padding: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}
.title {
    font-size: 24px;
    color: #3498db;
    font-weight: bold;
    margin-bottom: 20px;
}
.button {
    display: block;
    width: 100%;
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.3s;
    margin: 10px 0;
    background: lightgray;
    color: black;
}
.button:hover {
    background: gray;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="title">Admin Panel</div>
    <button class="button" onclick="location.href='{{
    route('main_page')}}'">Product Katalog</button>
    <button class="button" onclick="location.href='{{ route('products.create') }}'">Create New Product</button>
    <button class="button" onclick="location.href='{{ route('products.index') }}'">Manage Products</button>
</div>
@endsection
