import { Component } from '@angular/core';
import { QueryingService } from '../querying.service';

@Component({
  selector: 'app-tab1',
  templateUrl: 'tab1.page.html',
  styleUrls: ['tab1.page.scss']
})
export class Tab1Page {

  search = "";
  public queryingService: QueryingService;
  public items: Object;
  public searchNode: number;
  public directions: Array<string>

  constructor(queryingService: QueryingService) {
    this.queryingService = queryingService;
    this.items = {};
    this.directions = [];
  }

  submit(){
    this.query(this.search);
  }

  query(search){
    this.queryingService.postSearch(search).subscribe(response =>{
      this.items = response;
      this.searchNode = this.items[0].node;
      this.queryingService.navigate(this.searchNode).subscribe(instructions =>{
        this.directions = instructions;
      }
      );
    }
    );
  }

}

