import { Component } from '@angular/core';
import { BarcodeScanner } from '@ionic-native/barcode-scanner/ngx';
import { ToastController } from '@ionic/angular';
import { QueryingService } from '../querying.service';
import { Observable } from 'rxjs';
import { Item } from '../types';


@Component({
  selector: 'app-tab2',
  templateUrl: 'tab2.page.html',
  styleUrls: ['tab2.page.scss']
})
export class Tab2Page {
  scannedCode = null;
  public item_id: number;
  public queryingService: QueryingService;
  public itemDetails: Object;

  constructor(private barcodeScanner: BarcodeScanner,
    private toastCtrl: ToastController,
    queryingService: QueryingService) {
      this.item_id = 114;
      this.queryingService = queryingService;
      this.itemDetails = {};
    }

//barcode scanner implementation: https://www.youtube.com/watch?v=IadOort3Ns8
scanCode() {
  this.barcodeScanner.scan().then(
    barcodeData => {
      this.scannedCode = barcodeData.text;
    }
  )
  this.returnItem(this.scannedCode);  //change within brackets of this.returnItem() to this.scannedCode || this.item_id for testing
}

returnItem(item_id){
  this.queryingService.barcodeResult(item_id).subscribe(item => {
    this.itemDetails['item_id'] = item.item_id;
    this.itemDetails['price'] = item.price;
    this.itemDetails['color'] = item.color;
    this.itemDetails['size'] = item.size;
    this.itemDetails['material'] = item.material;
    this.itemDetails['category'] = item.category;
    this.itemDetails['node'] = item.node;
  }
    );
  console.log(this.itemDetails);
}


}