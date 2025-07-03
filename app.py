from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List, Optional

app = FastAPI(title="BeerHouse Orders API")

class OrderItem(BaseModel):
    name: str
    quantity: int

class Order(BaseModel):
    id: int
    table: int
    items: List[OrderItem]
    served: bool = False

orders = []

@app.get("/orders", response_model=List[Order])
def list_orders():
    return orders

@app.post("/orders", response_model=Order)
def create_order(order: Order):
    if any(o.id == order.id for o in orders):
        raise HTTPException(status_code=400, detail="Order ID already exists")
    orders.append(order)
    return order

@app.get("/orders/{order_id}", response_model=Order)
def get_order(order_id: int):
    for order in orders:
        if order.id == order_id:
            return order
    raise HTTPException(status_code=404, detail="Order not found")

@app.put("/orders/{order_id}", response_model=Order)
def update_order(order_id: int, updated: Order):
    for i, order in enumerate(orders):
        if order.id == order_id:
            orders[i] = updated
            return updated
    raise HTTPException(status_code=404, detail="Order not found")

@app.delete("/orders/{order_id}")
def delete_order(order_id: int):
    for i, order in enumerate(orders):
        if order.id == order_id:
            del orders[i]
            return {"detail": "Order deleted"}
    raise HTTPException(status_code=404, detail="Order not found")
