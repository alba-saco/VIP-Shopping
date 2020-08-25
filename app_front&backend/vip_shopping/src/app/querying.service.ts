import { Injectable } from '@angular/core';
import { HttpClient } from "@angular/common/http";
import { Observable } from 'rxjs';
import { Item } from './types';

@Injectable({
  providedIn: 'root'
})
export class QueryingService {

  constructor(private _httpClient: HttpClient) { }

  postSearch(search: string): Observable<Item>{
    this._httpClient.post<string>(API, search).subscribe();
    return this._httpClient.post<Item>(API, search);
  }

  navigate(node: number){
    this._httpClient.post<number>(nav_API, node).subscribe();
    return this._httpClient.post<Array<string>>(nav_API, node);
  }

  barcodeResult(item_id: number): Observable<Item>{
    this._httpClient.post<number>(barcode_API, item_id).subscribe();
    return this._httpClient.post<Item>(barcode_API, item_id);
  }
}

const API = "http://localhost:80/coursework/project/querying.php";
//const API = "http://192.168.1.71/coursework/project/querying.php";

const nav_API = "http://localhost:80/coursework/project/nav.php";
//const nav_API = "http://192.168.1.71/coursework/project/nav.php";

const barcode_API = "http://localhost:80/coursework/project/barcode_result.php";
//const barcode_API = "http://192.168.1.71/coursework/project/barcode_result.php";